import os
import asyncio
import logging
from metaapi_cloud_sdk import MetaApi
from dotenv import load_dotenv

load_dotenv()

# Suppress noisy internal MetaAPI SDK logs (keep only WARNING+)
logging.getLogger('metaapi_cloud_sdk').setLevel(logging.WARNING)

token    = os.getenv("META_API_TOKEN")
login    = os.getenv("MT5_LOGIN")
password = os.getenv("MT5_PASSWORD")
server   = os.getenv("MT5_SERVER")

async def provision_and_connect():
    api = MetaApi(token)
    connection = None

    try:
        # ── STEP 1: Register MT5 account with MetaAPI ──────────────────────
        print("Registering MT5 account with MetaAPI...")

        # Check if account already exists to avoid duplicate registrations
        accounts = await api.metatrader_account_api.get_accounts_with_infinite_scroll_pagination()
        account = next(
            (a for a in accounts if a.login == str(login) and a.server == server),
            None
        )

        if account:
            print(f"✓ Account already registered! MetaAPI ID: {account.id}")
        else:
            account = await api.metatrader_account_api.create_account({
                'name':        'JMJ Trading Account',
                'type':        'cloud',
                'login':       login,
                'password':    password,
                'server':      server,
                'platform':    'mt5',
                'application': 'MetaApi',
                'magic':       0,
            })
            print(f"✓ Account registered! MetaAPI ID: {account.id}")

        print(f"  State: {account.state}")

        # ── STEP 2: Deploy & wait for broker connection ────────────────────
        if account.state not in ('DEPLOYED', 'DEPLOYING'):
            print("\nDeploying account...")
            await account.deploy()

        print("\nWaiting for account to deploy (this may take ~60s on first run)...")
        await account.wait_deployed(timeout_in_seconds=120)
        print("✓ Account deployed!")

        # ── STEP 3: Connect and fetch account info ─────────────────────────
        print("\nConnecting to broker...")
        connection = account.get_rpc_connection()
        await connection.connect()

        print("Waiting for synchronization...")
        await connection.wait_synchronized(timeout_in_seconds=120)

        info = await connection.get_account_information()
        print(f"\n✓ Connected to broker!")
        print(f"  Account ID: {account.id}")
        print(f"  Balance:    {info['balance']} {info['currency']}")
        print(f"  Equity:     {info['equity']}")
        print(f"  Leverage:   {info['leverage']}")

        return account.id  # Return MetaAPI UUID to save in your DB

    except TimeoutError as e:
        print(f"✗ Timeout — broker may be offline or credentials incorrect:\n  {e}")
    except ConnectionError as e:
        print(f"✗ Connection error:\n  {e}")
    except Exception as e:
        error_type = type(e).__name__
        print(f"✗ Failed [{error_type}]: {e}")
    finally:
        if connection:
            try:
                await connection.close()
                print("\nConnection closed cleanly.")
            except Exception:
                pass  # Ignore close errors
        api.close()


if __name__ == "__main__":
    asyncio.run(provision_and_connect())