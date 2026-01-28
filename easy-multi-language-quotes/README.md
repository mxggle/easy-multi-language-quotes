# Easy Multi-Language Quotes

A WordPress plugin that displays random quotes in multiple languages (Chinese, English, and Japanese). It allows you to manage quotes via JSON or CSV and customize the display frequency.

## Features

- **Multi-Language Support**: Simultaneously displays quotes in Chinese (ZH), English (EN), and Japanese (JA).
- **Flexible Data Sources**: Import quotes by uploading a JSON or CSV file, or manually entering JSON data.
- **Customizable Frequency**: Set how often the quote updates:
  - Every Page Refresh
  - Hourly
  - Daily
- **Easy Integration**: Use the simple shortcode `[easy_multi_language_quote]` to place quotes anywhere on your site.
- **Clean Design**: Comes with a focused, elegant styling.

## Installation

1. Download the `easy-multi-language-quotes` folder.
2. Upload the entire folder to your WordPress plugins directory: `/wp-content/plugins/`.
3. Log in to your WordPress dashboard.
4. Go to **Plugins > Installed Plugins**.
5. Locate **Easy Multi-Language Quotes** and click **Activate**.

## Usage & Configuration

Once activated, you can configure the plugin settings:

1. Navigate to **Settings > Easy Quotes** in your WordPress dashboard.

### Adding Quotes

You can add quotes using one of two methods:

**Method A: Manual JSON Input**
- Select "Manual Input" as the Data Source.
- Paste your JSON data directly into the text area.

**Method B: File Upload**
- Select "File Upload" (or simply use the upload field which overlays manual input).
- Upload a valid `.json` or `.csv` file.
- *Note: Uploading a file will overwrite the existing manual input.*

### JSON Format
Your JSON file should be an array of objects. Each object requires an `author` and a `quote` object containing keys for `zh`, `en`, and `ja`.

```json
[
  {
    "author": "Confucius",
    "quote": {
      "zh": "学而时习之，不亦说乎？",
      "en": "Is it not pleasant to learn with a constant perseverance and application?",
      "ja": "学びて時にこれを習う、亦説ばしからずや。"
    }
  },
  {
    "author": "Steve Jobs",
    "quote": {
      "zh": "求知若饥，虚心若愚。",
      "en": "Stay hungry. Stay foolish.",
      "ja": "ハングリーであれ。愚直であれ。"
    }
  }
]
```

### CSV Format
If using CSV, ensure the first row contains the following headers: `author`, `zh`, `en`, `ja`.

```csv
author,zh,en,ja
Confucius,"学而时习之...","Is it not pleasant...","学びて時に..."
```

### Refresh Frequency
Choose how often users see a new quote under "Refresh Frequency":
- **Every Page Refresh**: A new quote is picked every time the page loads.
- **Hourly**: The quote remains the same for one hour.
- **Daily**: The quote changes once every 24 hours.

## Displaying Quotes

To display the quote block on a page or post, simply add the following shortcode:

```
[easy_multi_language_quote]
```

You can also use it in PHP templates:

```php
<?php echo do_shortcode('[easy_multi_language_quote]'); ?>
```
