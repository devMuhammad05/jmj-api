import os
import asyncio
from metaapi_cloud_sdk import MetaApi
from dotenv import load_dotenv

load_dotenv()

token      = os.getenv("META_API_TOKEN")
login      = os.getenv("MT5_LOGIN")       # your MT5 account number e.g. "123456"
password   = os.getenv("MT5_PASSWORD")    # your MT5 password
server     = os.getenv("MT5_SERVER")      # broker server name e.g. "Exness-MT5Real"


async def provision_and_connect():
    api = MetaApi(token)

    try:
        # STEP 1: Register your MT5 account with MetaAPI
        # This creates a MetaAPI-managed account and returns a MetaAPI UUID
        print("Registering MT5 account with MetaAPI...")
        account = await api.metatrader_account_api.create_account({
            'name': 'JMJ Trading Account',
            'type': 'cloud',            # MetaAPI hosts it in the cloud
            'login': login,             # your MT5 broker login number
            'password': password,       # your MT5 password
            'server': server,           # your broker's server name
            'platform': 'mt5',          # or 'mt4'
            'application': 'MetaApi',
            'magic': 0,
        })

        # SAVE THIS ID — store it in your DB as metaapi_account_id
        print(f"✓ Account registered! MetaAPI ID: {account.id}")
        print(f"  State: {account.state}")

        # STEP 2: Wait for it to deploy and connect to your broker
        print("\nWaiting for account to deploy (this may take ~60s on first run)...")
        await account.wait_deployed()
        print("✓ Account deployed!")

        # STEP 3: Connect and fetch account info
        connection = account.get_rpc_connection()
        await connection.connect()
        await connection.wait_synchronized()

        info = await connection.get_account_information()
        print(f"\n✓ Connected to broker!")
        print(f"  Balance:  {info['balance']} {info['currency']}")
        print(f"  Equity:   {info['equity']}")
        print(f"  Leverage: {info['leverage']}")

        await connection.close()

    except Exception as e:
        print(f"✗ Failed: {e}")

    finally:
        api.close()


if __name__ == "__main__":
    asyncio.run(provision_and_connect())