# Configurazione Mappa Google

## Introduzione

La pagina dei contatti supporta una mappa interattiva di Google Maps. Puoi scegliere tra due modalità:

1. **URL embed** - Modalità semplice (non interattiva)
2. **Mappa interattiva** - Richiede API key di Google Maps

## Modalità URL Embed (Semplice)

Nei settings admin, compila il campo **"URL embed Google Maps"**:

1. Vai su Google Maps
2. Fai clic su Condividi
3. Copia il link embed e incollalo nel campo

Questa modalità non richiede alcuna configurazione aggiuntiva.

## Modalità Mappa Interattiva (Consigliata)

### Step 1: Ottenere API Key

1. Vai a [Google Cloud Console](https://console.cloud.google.com)
2. Crea un nuovo progetto o selezionane uno esistente
3. Abilita le API:
   - **Maps JavaScript API**
   - **Places API** (opzionale)
4. Crea una chiave API (tipo: Chiave pubblica)
5. Configura le restrizioni:
   - Tipo: HTTP referrer
   - Referrer: `yourdomain.com/*`

### Step 2: Configurare Variabile Ambientale

Nel file `.env`, aggiungi:

```env
GOOGLE_MAPS_API_KEY=your_api_key_here
```

### Step 3: Configurare Latitudine e Longitudine

Nei settings admin, compila i campi:

- **Latitudine**: es. `45.5017`
- **Longitudine**: es. `-73.0952`
- **Zoom** (opzionale): tra 1 e 21 (default: 15)

Puoi trovare le coordinate di una posizione:
- [Google Maps](https://maps.google.com) - fai clic destro per copiare le coordinate
- [Nominatim](https://nominatim.openstreetmap.org/) - motore di ricerca open-source

## Priorità di Visualizzazione

La pagina dei contatti mostra:

1. **Mappa interattiva** se sono configurati latitudine, longitudine e API key
2. **Iframe embed** se solo l'URL embed è configurato
3. **Nulla** se nessuno dei due è configurato

## Sicurezza

- L'API key è visibile nel HTML pubblico (limitata ai referrer configurati)
- Configura sempre le restrizioni nella Google Cloud Console
- Monitora l'utilizzo nella console per evitare costi imprevisti

## Troubleshooting

### La mappa non si carica

1. Verifica che `GOOGLE_MAPS_API_KEY` sia impostato in `.env`
2. Verifica che latitudine e longitudine siano valide (numeri decimali)
3. Controlla la console del browser (F12) per eventuali errori
4. Verifica i referrer configurati nella Google Cloud Console

### "Invalid API Key"

1. La chiave potrebbe non essere attiva
2. Potrebbe avere restrizioni sui referrer che escludono il tuo dominio
3. Verifica che Maps JavaScript API sia abilitato nel progetto

### Map appears but no marker

Verifica che le coordinate siano nel formato corretto (numeri decimali con punto, non virgola).
