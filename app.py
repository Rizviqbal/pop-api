import requests
import re
import json

def make_api_request(url):
    """Function to make a GET request using requests."""
    try:
        response = requests.get(url)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f"Error making API request: {e}")
        return None

def scrape_data(url):
    """Function to scrape raw HTML from a URL."""
    try:
        response = requests.get(url)
        response.raise_for_status()
        return response.text
    except requests.exceptions.RequestException as e:
        print(f"Error scraping data: {e}")
        return None

# API URLs
api_key = 'KRFEAKW6HS5Z6TMDXRCKIM2QCY98UKI2NN'
pop_total_url = f"https://api.basescan.org/api?module=stats&action=tokensupply&contractaddress=0xe12acF5BB21654195a498c2FBd49fFf801A3A02d&apikey={api_key}"
pop_burn_url = f"https://api.basescan.org/api?module=account&action=tokenbalance&contractaddress=0xe12acF5BB21654195a498c2FBd49fFf801A3A02d&address=0x000000000000000000000000000000000000dead&apikey={api_key}"
pop_info_url = "https://api.dexscreener.com/latest/dex/tokens/0xe12acF5BB21654195a498c2FBd49fFf801A3A02d"
pop_holders_url = "https://basescan.org/token/0xe12acF5BB21654195a498c2FBd49fFf801A3A02d"

# Fetch API responses
pop_total_response = make_api_request(pop_total_url)
pop_burn_response = make_api_request(pop_burn_url)
pop_info_response = make_api_request(pop_info_url)
pop_holders_response = scrape_data(pop_holders_url)

# Decode JSON responses
if pop_total_response and "result" in pop_total_response:
    pop_total_data = int(pop_total_response["result"]) / 1e18
else:
    pop_total_data = 0

if pop_burn_response and "result" in pop_burn_response:
    pop_burn_data = int(pop_burn_response["result"]) / 1e18
else:
    pop_burn_data = 0

if pop_info_response and "pairs" in pop_info_response:
    pop_price = float(pop_info_response["pairs"][0]["priceUsd"])
    pop_market_cap = float(pop_info_response["pairs"][0]["marketCap"])
else:
    pop_price = 0
    pop_market_cap = 0

# Calculate Circulating Supply
pop_circulating_data = pop_total_data - pop_burn_data

# Format data for human readability
pop_total_number = f"{pop_total_data:,.0f}"
pop_burn_number = f"{pop_burn_data:,.2f}"
pop_circulating_number = f"{pop_circulating_data:,.2f}"
pop_price_formatted = f"{pop_price:,.4f}"
pop_market_cap_formatted = f"{pop_market_cap:,.0f}"

# Parse the holders count from the HTML
pop_holders = "Unable to scrape holders data."
if pop_holders_response:
    match = re.search(r'<div id="ContentPlaceHolder1_tr_tokenHolders">.*?<div class="d-flex flex-wrap gap-2">\s*<div>\s*(\d+)\s*</div>', pop_holders_response, re.DOTALL)
    if match:
        pop_holders = f"{int(match.group(1)):,.0f}"

# Output results
result = {
    # "Burned Supply": pop_burn_number,
    "result": pop_circulating_number,
}

print(json.dumps(result, indent=4))
