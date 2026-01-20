import cloudscraper, json
import pandas as pd
from time import sleep

# length 1 untuk 1 hari kerja
# karena kita mau ambil sehari aja
length = 1

# list emiten
emiten = pd.read_csv('data/List Emiten/all.csv')
lq45 = pd.read_csv('data/List Emiten/LQ45.csv')

# get kode-kode emiten
kode_emiten = emiten['code'].values
kode_lq45 = lq45['code'].values

# http client dengan browser headers
http = cloudscraper.create_scraper(
    browser={
        'browser': 'chrome',
        'platform': 'windows',
        'desktop': True
    }
)

# headers tambahan
headers = {
    'Accept': 'application/json, text/plain, */*',
    'Accept-Language': 'en-US,en;q=0.9,id;q=0.8',
    'Referer': 'https://www.idx.co.id/en/market-data/stocks-data/stock-list/',
    'Origin': 'https://www.idx.co.id',
    'sec-ch-ua': '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
    'sec-ch-ua-mobile': '?0',
    'sec-ch-ua-platform': '"Windows"',
    'sec-fetch-dest': 'empty',
    'sec-fetch-mode': 'cors',
    'sec-fetch-site': 'same-origin'
}

# Kunjungi halaman utama dulu untuk mendapatkan cookies
print("Mengunjungi halaman utama IDX untuk mendapatkan cookies...")
try:
    init_response = http.get('https://www.idx.co.id/en/market-data/stocks-data/stock-list/', headers=headers)
    print(f"Init response status: {init_response.status_code}")
    sleep(3)
except Exception as e:
    print(f"Warning: Gagal akses halaman utama: {e}")

print(f"\nTotal emiten: {len(kode_emiten)}")
print("Mulai mengambil data harian...\n")

for i, code in enumerate(kode_emiten):
    # link (gunakan www.idx.co.id)
    link = f"https://www.idx.co.id/primary/ListedCompany/GetTradingInfoSS?code={code}&length={length}"

    print(f"[{i+1}/{len(kode_emiten)}] Fetching {code}...", end=" ")

    # send request dengan retry
    max_retries = 3
    result = None
    
    for attempt in range(max_retries):
        try:
            response = http.get(link, headers=headers)
            
            if response.status_code == 429:
                print(f"Rate limited, waiting 30s...")
                sleep(30)
                continue
            
            if response.status_code != 200:
                print(f"Error {response.status_code}")
                break
            
            result = json.loads(response.text)
            break
        except json.JSONDecodeError as e:
            print(f"JSON error: {e}")
            break
        except Exception as e:
            print(f"Error: {e}, retry {attempt+1}/{max_retries}")
            sleep(10)
    
    if result is None:
        print("Skipped")
        continue

    # ada isinya?
    if not result.get("replies"):
        print("Tidak ada data")
        continue

    # load data lama
    try:
        history = pd.read_csv(f"data/Saham/Semua/{code}.csv")
    except:
        # ga ada data lama, baru IPO
        history = pd.DataFrame({
            'date': [],
            'previous': [],
            'open_price': [],
            'first_trade': [],
            'high': [],
            'low': [],
            'close': [],
            'change': [],
            'volume': [],
            'value': [],
            'frequency': [],
            'index_individual': [],
            'offer': [],
            'offer_volume': [],
            'bid': [],
            'bid_volume': [],
            'listed_shares': [],
            'tradeble_shares': [],
            'weight_for_index': [],
            'foreign_sell': [],
            'foreign_buy': [],
            'delisting_date': [],
            'non_regular_volume': [],
            'non_regular_value': [],
            'non_regular_frequency': [],
        })

    # data-data
    date = []
    previous = []
    openPrice = []
    firstTrade = []
    high = []
    low = []
    close = []
    change = []
    volume = []
    value = []
    frequency = []
    indexIndividual = []
    offer = []
    offerVolume = []
    bid = []
    bidVolume = []
    listedShares = []
    tradebleShares = []
    weightForIndex = []
    foreignSell = []
    foreignBuy = []
    delistingDate = []
    nonRegularVolume = []
    nonRegularValue = []
    nonRegularFrequency = []

    # simpan data-data (hanya yang belum ada)
    new_count = 0
    for data in result["replies"][::-1]:
        if data['Date'] not in history.date.values:
            date.append(data['Date'])
            previous.append(data['Previous'])
            openPrice.append(data['OpenPrice'])
            firstTrade.append(data['FirstTrade'])
            high.append(data['High'])
            low.append(data['Low'])
            close.append(data['Close'])
            change.append(data['Change'])
            volume.append(data['Volume'])
            value.append(data['Value'])
            frequency.append(data['Frequency'])
            indexIndividual.append(data['IndexIndividual'])
            offer.append(data['Offer'])
            offerVolume.append(data['OfferVolume'])
            bid.append(data['Bid'])
            bidVolume.append(data['BidVolume'])
            listedShares.append(data['ListedShares'])
            tradebleShares.append(data['TradebleShares'])
            weightForIndex.append(data['WeightForIndex'])
            foreignSell.append(data['ForeignSell'])
            foreignBuy.append(data['ForeignBuy'])
            delistingDate.append(data['DelistingDate'])
            nonRegularVolume.append(data['NonRegularVolume'])
            nonRegularValue.append(data['NonRegularValue'])
            nonRegularFrequency.append(data['NonRegularFrequency'])
            new_count += 1

    # data beres, simpan dalam CSV
    hari_ini = pd.DataFrame({
        'date': date,
        'previous': previous,
        'open_price': openPrice,
        'first_trade': firstTrade,
        'high': high,
        'low': low,
        'close': close,
        'change': change,
        'volume': volume,
        'value': value,
        'frequency': frequency,
        'index_individual': indexIndividual,
        'offer': offer,
        'offer_volume': offerVolume,
        'bid': bid,
        'bid_volume': bidVolume,
        'listed_shares': listedShares,
        'tradeble_shares': tradebleShares,
        'weight_for_index': weightForIndex,
        'foreign_sell': foreignSell,
        'foreign_buy': foreignBuy,
        'delisting_date': delistingDate,
        'non_regular_volume': nonRegularVolume,
        'non_regular_value': nonRegularValue,
        'non_regular_frequency': nonRegularFrequency,
    })

    # jadikan satu dgn yang lama
    new_data = pd.concat([history, hari_ini], ignore_index=True)

    # simpan
    new_data.to_csv(f"data/Saham/Semua/{code}.csv", index=False)

    # Saham LQ45?
    if code in kode_lq45:
        new_data.to_csv(f"data/Saham/LQ45/{code}.csv", index=False)

    if new_count > 0:
        print(f"OK (+{new_count} hari baru)")
    else:
        print("OK (sudah up-to-date)")

    # delay untuk menghindari rate limiting
    sleep(2)

print("\nSelesai!")
