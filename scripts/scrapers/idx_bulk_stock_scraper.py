#!/usr/bin/env python3
"""
IDX Bulk Stock Trading Summary Scraper

This script scrapes all stock trading summary data from IDX in bulk using cloudscraper
to bypass Cloudflare protection. Based on the working get-data-harian-emiten.py script.

Usage:
    python idx_bulk_stock_scraper.py [date]

Arguments:
    date: Optional date in YYYYMMDD format. If not provided, uses today's date.

Output:
    Saves JSON file with format YYYYMMDD.json in the trading-data directory
"""

import cloudscraper
import json
import sys
from datetime import datetime, timedelta
from pathlib import Path
from typing import Optional
from time import sleep

class IDXBulkStockScraper:
    """IDX Bulk Stock Trading Summary Scraper using cloudscraper"""

    BASE_URL = "https://www.idx.co.id/primary/TradingSummary/GetStockSummary"

    def __init__(self, output_dir: str = "storage/app/trading-data"):
        """
        Initialize the scraper

        Args:
            output_dir: Directory to save JSON files
        """
        self.output_dir = Path(output_dir)
        self.output_dir.mkdir(parents=True, exist_ok=True)

        # Initialize cloudscraper with browser simulation
        self.http = cloudscraper.create_scraper(
            browser={
                'browser': 'chrome',
                'platform': 'windows',
                'desktop': True
            }
        )

    def get_date_string(self, date: Optional[str] = None) -> str:
        """
        Get date string in YYYYMMDD format

        Args:
            date: Optional date string in YYYYMMDD format

        Returns:
            Date string in YYYYMMDD format
        """
        if date:
            try:
                # Validate date format
                datetime.strptime(date, "%Y%m%d")
                return date
            except ValueError:
                print(f"Invalid date format: {date}. Using today's date.")
                return self.get_today_string()
        else:
            return self.get_today_string()

    def get_today_string(self) -> str:
        """Get today's date in YYYYMMDD format"""
        return datetime.now().strftime("%Y%m%d")

    def scrape_data(self, date: Optional[str] = None) -> bool:
        """
        Scrape all stock trading data for the given date

        Args:
            date: Date in YYYYMMDD format (optional, defaults to today)

        Returns:
            True if successful, False otherwise
        """
        date_str = self.get_date_string(date)
        print(f"Scraping bulk stock data for date: {date_str} using cloudscraper")

        # Prepare API parameters
        params = {
            "length": 9999,
            "start": 0,
            "date": date_str
        }

        try:
            # Headers based on working script
            headers = {
                'Accept': 'application/json, text/plain, */*',
                'Accept-Language': 'en-US,en;q=0.9,id;q=0.8',
                'Referer': 'https://www.idx.co.id/en/market-data/trading-summary/stock-summary/',
                'Origin': 'https://www.idx.co.id',
                'sec-ch-ua': '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                'sec-ch-ua-mobile': '?0',
                'sec-ch-ua-platform': '"Windows"',
                'sec-fetch-dest': 'empty',
                'sec-fetch-mode': 'cors',
                'sec-fetch-site': 'same-origin'
            }

            # First visit the main page to establish session (like the working script)
            print("Visiting IDX main page to establish session...")
            try:
                init_response = self.http.get('https://www.idx.co.id/en/market-data/stocks-data/stock-list/', headers=headers)
                print(f"Init response status: {init_response.status_code}")
                sleep(3)
            except Exception as e:
                print(f"Warning: Failed to access main page: {e}")

            # Also visit the trading summary page
            print("Visiting trading summary page...")
            try:
                summary_response = self.http.get('https://www.idx.co.id/en/market-data/trading-summary/stock-summary/', headers=headers)
                print(f"Summary page response status: {summary_response.status_code}")
                sleep(2)
            except Exception as e:
                print(f"Warning: Failed to access summary page: {e}")

            # Now make the API request
            print(f"Making API request to: {self.BASE_URL}")

            # Try with retry logic like the working script
            max_retries = 3
            result = None

            for attempt in range(max_retries):
                try:
                    print(f"Attempt {attempt + 1}/{max_retries}...")
                    response = self.http.get(self.BASE_URL, params=params, headers=headers)

                    if response.status_code == 429:
                        print("Rate limited, waiting 30s...")
                        sleep(30)
                        continue

                    if response.status_code != 200:
                        print(f"HTTP Error {response.status_code}")
                        if attempt < max_retries - 1:
                            sleep(10)
                            continue
                        else:
                            print(f"Failed after {max_retries} attempts")
                            return False

                    # Try to parse JSON
                    try:
                        result = response.json()
                        print("Successfully parsed JSON response")
                        break
                    except json.JSONDecodeError as e:
                        print(f"JSON decode error: {e}")
                        print(f"Response content: {response.text[:500]}...")
                        return False

                except Exception as e:
                    print(f"Request error: {e}")
                    if attempt < max_retries - 1:
                        sleep(10)
                    else:
                        print(f"Failed after {max_retries} attempts")
                        return False

            if result is None:
                print("No data received")
                return False

            # Validate response structure
            if not isinstance(result, dict):
                print("Error: Invalid response format")
                return False

            # Check if we have data
            records = result.get("data", [])
            if not records:
                print("Warning: No trading data found in response")
                return False

            print(f"Retrieved {len(records)} trading records")

            # Add metadata to the data
            result["scraped_at"] = datetime.now().isoformat()
            result["date_requested"] = date_str
            result["scraping_method"] = "cloudscraper"

            # Save to file
            filename = f"{date_str}.json"
            filepath = self.output_dir / filename

            with open(filepath, 'w', encoding='utf-8') as f:
                json.dump(result, f, indent=2, ensure_ascii=False)

            print(f"Data saved to: {filepath}")
            print(f"File size: {filepath.stat().st_size} bytes")

            return True

        except Exception as e:
            print(f"Unexpected error: {e}")
            return False

def main():
    """Main function"""
    # Get date from command line argument if provided
    date_arg = sys.argv[1] if len(sys.argv) > 1 else None

    # Initialize scraper
    scraper = IDXBulkStockScraper()

    # Scrape data
    success = scraper.scrape_data(date_arg)

    # Exit with appropriate code
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()
