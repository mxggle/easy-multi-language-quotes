# 🌟 Easy Multi-Language Quotes

[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![WordPress Version](https://img.shields.io/badge/WordPress-5.0%2B-0073AA.svg)](https://wordpress.org/)

A elegant and powerful WordPress plugin designed to display random quotes in multiple languages (**Chinese, English, and Japanese**) simultaneously. Perfect for multilingual blogs, educational sites, or anyone wanting to add a touch of global inspiration to their pages.

---

## ✨ Features

- 🌍 **Multi-Language Support**: Displays quotes in Chinese (ZH), English (EN), and Japanese (JA) at once.
- 📂 **Flexible Data Import**:
    - **JSON/CSV Upload**: Easily bulk-import your favorite quotes.
    - **Manual JSON Input**: Paste and save your collection directly in the dashboard.
- ⏱️ **Configurable Display Frequency**:
    - **Every Page Refresh**: Keep it dynamic!
    - **Hourly**: Change it up gently.
    - **Daily**: A steady "Quote of the Day".
- 🎨 **Minimalist Design**: Sleek, modern styling that blends seamlessly into any WordPress theme.
- 🧩 **Shortcode Ready**: Drop `[easy_multi_language_quote]` anywhere—posts, pages, or widgets.

---

## 🚀 Quick Start

### Installation

1.  **Download/Clone**: Clone this repository into your WordPress `wp-content/plugins/` directory:
    ```bash
    git clone https://github.com/mxggle/easy-multi-language-quotes.git
    ```
2.  **Activate**: Log in to your WordPress Admin dashboard, go to **Plugins**, and click **Activate** on "Easy Multi-Language Quotes".
3.  **Configure**: Visit **Settings > Easy Quotes** to upload your quote library or paste your JSON.

### Displaying Quotes

Simply add the shortcode to any post or page:
```text
[easy_multi_language_quote]
```

Or use it directly in your PHP templates:
```php
<?php echo do_shortcode('[easy_multi_language_quote]'); ?>
```

---

## 📊 Data Formats

### JSON Format
The plugin expects an array of objects. Each object should have an `author` and a `quote` map containing `zh`, `en`, and `ja` keys.

```json
[
  {
    "author": "Confucius",
    "quote": {
      "zh": "学而时习之，不亦说乎？",
      "en": "Is it not pleasant to learn with a constant perseverance and application?",
      "ja": "学びて時にこれを習う、亦説ばしからずや。"
    }
  }
]
```

### CSV Format
Ensure your CSV has the column headers: `author`, `zh`, `en`, `ja`.

```csv
author,zh,en,ja
"Lao Tzu","千里之行，始于足下。","A journey of a thousand miles begins with a single step.","千里の行も足下より始まる。"
```

---

## 📂 Project Structure

```bash
easy-multi-language-quotes/
├── admin/                 # Admin-side PHP logic (settings page)
├── public/                # Public-facing logic, CSS, and JS
│   ├── css/               # Styling for the quote block
│   └── js/                # Client-side randomization/fetching
├── easy-multi-language-quotes.php # Main plugin entry point
├── qutes.json             # Example quote database
└── README.md              # Technical documentation
```

---

## 🛠️ Requirements

- **WordPress**: 5.0+
- **PHP**: 7.4+
- **Languages Supported**: Chinese (Simplified), English, Japanese.

---

## 📄 License

This project is licensed under the **GPL-2.0+ License**. Feel free to use, modify, and distribute.

---

*Made with ❤️ for the multilingual web.*
