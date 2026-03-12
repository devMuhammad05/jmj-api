import os
import asyncio
import logging
from datetime import datetime, timedelta
from metaapi_cloud_sdk import MetaStats
from dotenv import load_dotenv

load_dotenv()

# Suppress noisy SDK logs
logging.getLogger('metaapi_cloud_sdk').setLevel(logging.WARNING)

token      = os.getenv("META_API_TOKEN")
account_id = os.getenv("META_API_ACCOUNT_ID")  # MetaAPI UUID (not MT5 login)


def fmt(label: str, value) -> None:
    """Pretty-print a labelled result."""
    print(f"\n{'─' * 60}")
    print(f"  {label}")
    print(f"{'─' * 60}")
    if isinstance(value, dict):
        for k, v in value.items():
            print(f"  {k:<35} {v}")
    elif isinstance(value, list):
        if not value:
            print("  (no records)")
        for i, item in enumerate(value, 1):
            print(f"\n  [{i}]")
            if isinstance(item, dict):
                for k, v in item.items():
                    print(f"    {k:<33} {v}")
            else:
                print(f"  {item}")
    else:
        print(f"  {value}")


async def get_stats():
    api = MetaStats(token=token)

    try:
        # ── 1. Basic account metrics ───────────────────────────────────────
        print("\n⏳ Fetching account metrics...")
        metrics = await api.get_metrics(account_id=account_id)
        fmt("ACCOUNT METRICS (closed positions only)", metrics)

        # ── 2. Metrics including open positions ───────────────────────────
        print("\n⏳ Fetching metrics with open positions...")
        metrics_open = await api.get_metrics(
            account_id=account_id,
            include_open_positions=True
        )
        fmt("ACCOUNT METRICS (including open positions)", metrics_open)

        # ── 3. Historical trades (last 90 days by default) ────────────────
        end_time   = datetime.utcnow()
        start_time = end_time - timedelta(days=90)

        start_str = start_time.strftime("%Y-%m-%d %H:%M:%S.000")
        end_str   = end_time.strftime("%Y-%m-%d %H:%M:%S.000")

        print(f"\n⏳ Fetching trades from {start_str} → {end_str}...")
        trades = await api.get_account_trades(
            account_id=account_id,
            start_time=start_str,
            end_time=end_str,
        )
        fmt(f"HISTORICAL TRADES ({start_str} → {end_str})", trades)

        # ── 4. Currently open trades ──────────────────────────────────────
        print("\n⏳ Fetching open trades...")
        open_trades = await api.get_account_open_trades(account_id=account_id)
        fmt("OPEN TRADES", open_trades)

        # ── 5. Summary ────────────────────────────────────────────────────
        print(f"\n{'═' * 60}")
        print("  SUMMARY")
        print(f"{'═' * 60}")
        print(f"  Account ID   : {account_id}")

        # Safely extract common top-level metric fields
        for field in ('balance', 'equity', 'profit', 'deposits',
                      'wonTradesPercent', 'lostTradesPercent',
                      'trades', 'profitFactor', 'sharpeRatio'):
            val = metrics.get(field, 'n/a')
            print(f"  {field:<20} {val}")

        print(f"  Open trades  : {len(open_trades) if isinstance(open_trades, list) else 'n/a'}")
        print(f"  Recent trades: {len(trades) if isinstance(trades, list) else 'n/a'} "
              f"(last 90 days)")
        print(f"{'═' * 60}\n")

    except Exception as e:
        error_type = type(e).__name__
        print(f"\n✗ Failed [{error_type}]: {e}")

    finally:
        # MetaStats doesn't require explicit close, but good practice
        try:
            api.close()
        except Exception:
            pass


# ── Optional: reset metrics (destructive — commented out by default) ──────────
async def reset_stats():
    """
    WARNING: This permanently resets all account metrics.
    Uncomment the call in __main__ only when intentional.
    """
    api = MetaStats(token=token)
    try:
        print("\n⚠️  Resetting account metrics...")
        result = await api.reset_metrics(account_id=account_id)
        print(f"✓ Metrics reset: {result}")
    except Exception as e:
        print(f"✗ Reset failed [{type(e).__name__}]: {e}")
    finally:
        try:
            api.close()
        except Exception:
            pass


if __name__ == "__main__":
    asyncio.run(get_stats())

    # Uncomment ONLY when you intentionally want to wipe metrics:
    # asyncio.run(reset_stats())