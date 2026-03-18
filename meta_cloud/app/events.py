import asyncio
import logging
import requests
from metaapi_cloud_sdk import MetaApi
from dotenv import load_dotenv
import os

# Load environment variables
load_dotenv()
TOKEN = os.getenv("META_API_TOKEN")
ACCOUNT_ID = os.getenv("META_API_ACCOUNT_ID")  # MetaAPI UUID
LARAVEL_API_URL = os.getenv("LARAVEL_API_URL")  # e.g., 
LARAVEL_API_TOKEN = os.getenv("LARAVEL_API_TOKEN")  # optional auth for Laravel

# Suppress verbose MetaAPI logs
logging.getLogger('metaapi_cloud_sdk').setLevel(logging.WARNING)

# Helper to post events to Laravel
def post_to_laravel(event_type: str, payload: dict):
    data = {
        "event_type": event_type,
        "account_id": ACCOUNT_ID,
        "payload": payload
    }
    headers = {"Authorization": f"Bearer {LARAVEL_API_TOKEN}"} if LARAVEL_API_TOKEN else {}
    try:
        requests.post(LARAVEL_API_URL, json=data, headers=headers, timeout=10)
    except Exception as e:
        print(f"❌ Failed to send {event_type} to Laravel: {e}")

async def main():
    api = MetaApi(TOKEN)

    # Get MetaAPI account object
    account = await api.metatrader_account_api.get_account(ACCOUNT_ID)

    # Connect to RPC/WebSocket
    connection = account.get_rpc_connection()
    await connection.connect()
    await connection.wait_synchronized()
    print("✅ Connected to MetaAPI WebSocket")

    # ----------------------
    # Real-time event handlers
    # ----------------------

    @connection.on('balanceUpdated')
    def handle_balance_update(balance):
        print(f"Balance updated: {balance}")
        post_to_laravel("balanceUpdated", balance)

    @connection.on('equityUpdated')
    def handle_equity_update(equity):
        print(f"Equity updated: {equity}")
        post_to_laravel("equityUpdated", equity)

    @connection.on('positionOpened')
    def handle_position_opened(position):
        print(f"Position opened: {position}")
        post_to_laravel("positionOpened", position)

    @connection.on('positionClosed')
    def handle_position_closed(position):
        print(f"Position closed: {position}")
        post_to_laravel("positionClosed", position)

    @connection.on('marginCall')
    def handle_margin_call(data):
        print(f"Margin call: {data}")
        post_to_laravel("marginCall", data)

    # Keep the script running indefinitely
    while True:
        await asyncio.sleep(60)

if __name__ == "__main__":
    asyncio.run(main())
