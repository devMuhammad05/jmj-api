# meta_cloud

A Python microservice that connects the JMJ Trading Platform to the [MetaAPI Cloud SDK](https://metaapi.cloud/) to provision and sync MetaTrader 4/5 accounts.

---

## What It Does

1. **Registers** an investor's MT4/MT5 account with MetaAPI Cloud
2. **Waits** for the account to deploy and connect to the broker
3. **Fetches** live account information (balance, equity, leverage)

The MetaAPI account ID returned during provisioning should be stored in the Laravel database (`meta_trader_credentials` table) for use in future sync jobs.

---

## Requirements

- Python 3.8+
- A [MetaAPI Cloud](https://metaapi.cloud/) account and API token
- An active MT4 or MT5 broker account

---

## Setup

**1. Create and activate a virtual environment**

```bash
cd meta_cloud
python -m venv venv

# Windows
venv\Scripts\activate

# macOS / Linux
source venv/bin/activate
```

**2. Install dependencies**

```bash
pip install metaapi-cloud-sdk python-dotenv
```

**3. Configure environment variables**

Copy the example file and fill in your credentials:

```bash
cp .env.example .env
```

Edit `.env`:

```env
META_API_TOKEN=your_metaapi_token_here
MT5_LOGIN=123456
MT5_PASSWORD=your_mt5_password
MT5_SERVER=Exness-MT5Real
```

> **Never commit `.env` to version control.** It is already listed in `.gitignore`.

---

## Running

```bash
python app/main.py
```

**Expected output (first run):**

```
Registering MT5 account with MetaAPI...
✓ Account registered! MetaAPI ID: abc123-xxxx-xxxx-xxxx
  State: DEPLOYING

Waiting for account to deploy (this may take ~60s on first run)...
✓ Account deployed!

✓ Connected to broker!
  Balance:  5000.00 USD
  Equity:   4998.25
  Leverage: 100
```



---

## Project Structure

```
meta_cloud/
├── app/
│   └── main.py        # Entry point — provision & connect
├── .env               # Local secrets (git-ignored)
├── .env.example       # Safe template to commit
├── .gitignore
└── README.md
```

---

## Environment Variables

| Variable         | Description                                    |
| ---------------- | ---------------------------------------------- |
| `META_API_TOKEN` | Your MetaAPI Cloud API token                   |
| `MT5_LOGIN`      | MT5 broker account number (login ID)           |
| `MT5_PASSWORD`   | MT5 broker account password                    |
| `MT5_SERVER`     | MT5 broker server name (e.g. `Exness-MT5Real`) |
