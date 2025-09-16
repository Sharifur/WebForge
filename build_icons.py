#!/usr/bin/env python3
"""
Line Awesome Icons JSON Builder
Processes the complete list of Line Awesome icons and creates a structured JSON file
"""

import json
import re
from typing import Dict, List, Tuple

def categorize_icon(icon_name: str) -> Tuple[str, List[str]]:
    """Categorize icon and generate keywords based on its name"""

    # Define category mappings based on icon patterns
    categories = {
        # Brand icons
        'brand': [
            '500px', 'accusoft', 'adobe', 'affiliatetheme', 'airbnb', 'algolia', 'alipay',
            'amazon', 'amazon-pay', 'android', 'angellist', 'angrycreative', 'angular',
            'app-store', 'app-store-ios', 'apper', 'apple-pay', 'artstation', 'asymmetrik',
            'atlassian', 'audible', 'autoprefixer', 'avianex', 'aviato', 'aws', 'bandcamp',
            'battle-net', 'behance', 'behance-square', 'bimobject', 'bitbucket',
            'bitbucket-square', 'bitcoin', 'bity', 'black-tie', 'blackberry', 'blogger',
            'blogger-b', 'bootstrap', 'btc', 'buffer', 'buromobelexperte', 'buy-n-large',
            'buysellads', 'centercode', 'centos', 'chrome', 'chromecast', 'cloudscale',
            'cloudsmith', 'cloudversify', 'codepen', 'codiepie', 'confluence', 'connectdevelop',
            'contao', 'cotton-bureau', 'cpanel', 'creative-commons', 'critical-role',
            'css3', 'css3-alt', 'cuttlefish', 'd-and-d', 'd-and-d-beyond', 'dashcube',
            'delicious', 'deploydog', 'deskpro', 'dev', 'deviantart', 'digg', 'digital-ocean',
            'discord', 'discourse', 'dochub', 'docker', 'draft2digital', 'dribbble',
            'dribbble-square', 'dropbox', 'drupal', 'dyalog', 'earlybirds', 'ebay', 'edge',
            'elementor', 'ello', 'ember', 'empire', 'envira', 'erlang', 'ethereum', 'etsy',
            'evernote', 'expeditedssl', 'facebook', 'facebook-f', 'facebook-messenger',
            'facebook-square', 'fantasy-flight-games', 'fedex', 'fedora', 'figma', 'firefox',
            'first-order', 'first-order-alt', 'firstdraft', 'flickr', 'flipboard', 'fly',
            'font-awesome', 'font-awesome-alt', 'font-awesome-flag', 'fonticons', 'fonticons-fi',
            'fort-awesome', 'fort-awesome-alt', 'forumbee', 'foursquare', 'free-code-camp',
            'freebsd', 'fulcrum', 'galactic-republic', 'galactic-senate', 'get-pocket', 'gg',
            'gg-circle', 'git', 'git-alt', 'git-square', 'github', 'github-alt', 'github-square',
            'gitkraken', 'gitlab', 'gitter', 'glide', 'glide-g', 'gofore', 'goodreads',
            'goodreads-g', 'google', 'google-drive', 'google-play', 'google-plus',
            'google-plus-g', 'google-plus-square', 'google-wallet', 'gratipay', 'grav',
            'gripfire', 'grunt', 'gulp', 'hacker-news', 'hacker-news-square', 'hackerrank',
            'hips', 'hire-a-helper', 'hooli', 'hornbill', 'hotjar', 'houzz', 'html5',
            'hubspot', 'imdb', 'instagram', 'intercom', 'internet-explorer', 'invision',
            'ioxhost', 'itch-io', 'itunes', 'java', 'jenkins', 'jira', 'joget', 'joomla',
            'js', 'js-square', 'jsfiddle', 'kaggle', 'keybase', 'keycdn', 'kickstarter',
            'kickstarter-k', 'korvue', 'laravel', 'lastfm', 'lastfm-square', 'leanpub',
            'less', 'line', 'linkedin', 'linkedin-in', 'linkedin-square', 'linode', 'linux',
            'lyft', 'magento', 'mailchimp', 'mandalorian', 'markdown', 'mastodon', 'maxcdn',
            'mdb', 'meanpath', 'medapps', 'medium', 'medium-m', 'medrt', 'meetup', 'megaport',
            'mendeley', 'microsoft', 'mix', 'mixcloud', 'mizuni', 'modx', 'monero', 'napster',
            'neos', 'nimblr', 'node', 'node-js', 'npm', 'ns8', 'nutritionix', 'odnoklassniki',
            'odnoklassniki-square', 'old-republic', 'opencart', 'openid', 'opera', 'optin-monster',
            'orcid', 'osi', 'page4', 'pagelines', 'palfed', 'patreon', 'paypal', 'penny-arcade',
            'periscope', 'phabricator', 'phoenix-framework', 'phoenix-squadron', 'php',
            'pied-piper', 'pied-piper-alt', 'pied-piper-hat', 'pied-piper-pp', 'pinterest',
            'pinterest-p', 'pinterest-square', 'playstation', 'product-hunt', 'pushed', 'python',
            'qq', 'quinscape', 'quora', 'r-project', 'raspberry-pi', 'ravelry', 'react',
            'reacteurope', 'readme', 'rebel', 'red-river', 'reddit', 'reddit-alien',
            'reddit-square', 'redhat', 'renren', 'replyd', 'researchgate', 'resistance',
            'resolving', 'rev', 'rocketchat', 'rockrms', 's15', 'safari', 'salesforce',
            'sass', 'schlix', 'scribd', 'searchengin', 'sellcast', 'sellsy', 'servicestack',
            'shirtsinbulk', 'shopware', 'simplybuilt', 'sistrix', 'sith', 'sketch', 'skyatlas',
            'skype', 'slack', 'slack-hash', 'slideshare', 'snapchat', 'snapchat-ghost',
            'snapchat-square', 'soundcloud', 'sourcetree', 'speakap', 'speaker-deck',
            'spotify', 'squarespace', 'stack-exchange', 'stack-overflow', 'stackpath',
            'staylinked', 'steam', 'steam-square', 'steam-symbol', 'sticker-mule', 'strava',
            'stripe', 'stripe-s', 'studiovinari', 'stumbleupon', 'stumbleupon-circle',
            'superpowers', 'supple', 'suse', 'swift', 'symfony', 'teamspeak', 'telegram',
            'telegram-plane', 'tencent-weibo', 'the-red-yeti', 'themeco', 'themeisle',
            'think-peaks', 'trade-federation', 'trello', 'tripadvisor', 'tumblr', 'tumblr-square',
            'twitch', 'twitter', 'twitter-square', 'typo3', 'uber', 'ubuntu', 'uikit',
            'umbraco', 'uniregistry', 'untappd', 'ups', 'usps', 'ussunnah', 'vaadin',
            'viacoin', 'viadeo', 'viadeo-square', 'viber', 'vimeo', 'vimeo-square', 'vimeo-v',
            'vine', 'vk', 'vnv', 'vuejs', 'waze', 'wechat', 'weebly', 'weibo', 'weixin',
            'whatsapp', 'whatsapp-square', 'whmcs', 'wikipedia-w', 'windows', 'wix',
            'wizards-of-the-coast', 'wolf-pack-battalion', 'wordpress', 'wordpress-simple',
            'wpbeginner', 'wpexplorer', 'wpforms', 'wpressr', 'xbox', 'xing', 'xing-square',
            'y-combinator', 'y-combinator-square', 'yahoo', 'yammer', 'yandex',
            'yandex-international', 'yarn', 'yc', 'yc-square', 'yelp', 'yoast', 'youtube',
            'youtube-square', 'zhihu'
        ],

        # Accessibility icons
        'accessibility': [
            'accessible-icon', 'american-sign-language-interpreting', 'assistive-listening-systems',
            'audio-description', 'blind', 'braille', 'deaf', 'deafness', 'hard-of-hearing',
            'low-vision', 'sign-language', 'universal-access', 'wheelchair', 'wheelchair-alt'
        ],

        # Arrows and directions
        'arrows': [
            'angle-double-down', 'angle-double-left', 'angle-double-right', 'angle-double-up',
            'angle-down', 'angle-left', 'angle-right', 'angle-up', 'arrow-alt-circle-down',
            'arrow-alt-circle-left', 'arrow-alt-circle-right', 'arrow-alt-circle-up',
            'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-right', 'arrow-circle-up',
            'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up', 'arrows', 'arrows-alt',
            'arrows-alt-h', 'arrows-alt-v', 'arrows-h', 'arrows-v', 'caret-down', 'caret-left',
            'caret-right', 'caret-up', 'chevron-circle-down', 'chevron-circle-left',
            'chevron-circle-right', 'chevron-circle-up', 'chevron-down', 'chevron-left',
            'chevron-right', 'chevron-up', 'compress', 'compress-arrows-alt', 'exchange',
            'exchange-alt', 'expand', 'expand-arrows-alt', 'external-link', 'external-link-alt',
            'external-link-square', 'external-link-square-alt', 'level-down', 'level-down-alt',
            'level-up', 'level-up-alt', 'location-arrow', 'long-arrow-alt-down',
            'long-arrow-alt-left', 'long-arrow-alt-right', 'long-arrow-alt-up', 'long-arrow-down',
            'long-arrow-left', 'long-arrow-right', 'long-arrow-up', 'random', 'redo', 'redo-alt',
            'reply', 'reply-all', 'retweet', 'share', 'share-alt', 'share-alt-square',
            'share-square', 'step-backward', 'step-forward', 'sync', 'sync-alt', 'undo',
            'undo-alt'
        ],

        # Medical and health
        'medical': [
            'allergies', 'ambulance', 'band-aid', 'briefcase-medical', 'capsules', 'clinic-medical',
            'comment-medical', 'diagnoses', 'dna', 'file-medical', 'file-medical-alt',
            'first-aid', 'heartbeat', 'hospital', 'hospital-alt', 'hospital-symbol',
            'laptop-medical', 'medkit', 'microscope', 'notes-medical', 'pills', 'plus',
            'prescription', 'prescription-bottle', 'prescription-bottle-alt', 'procedures',
            'stethoscope', 'syringe', 'tablets', 'teeth', 'teeth-open', 'thermometer',
            'tooth', 'user-md', 'user-nurse', 'vial', 'vials', 'x-ray'
        ],

        # Communication
        'communication': [
            'address-book', 'address-card', 'at', 'bell', 'bell-slash', 'bullhorn', 'comment',
            'comment-alt', 'comment-dots', 'comment-slash', 'commenting', 'comments', 'envelope',
            'envelope-open', 'envelope-open-text', 'envelope-square', 'fax', 'inbox', 'language',
            'mail-bulk', 'microphone', 'microphone-alt', 'microphone-alt-slash', 'microphone-slash',
            'mobile', 'mobile-alt', 'phone', 'phone-alt', 'phone-slash', 'phone-square',
            'phone-square-alt', 'phone-volume', 'rss', 'rss-square', 'satellite', 'satellite-dish',
            'sms', 'voicemail', 'volume-down', 'volume-mute', 'volume-off', 'volume-up', 'wifi'
        ],

        # Files and documents
        'files': [
            'archive', 'book', 'book-dead', 'book-medical', 'book-open', 'book-reader',
            'bookmark', 'clipboard', 'clipboard-check', 'clipboard-list', 'copy', 'file',
            'file-alt', 'file-archive', 'file-audio', 'file-code', 'file-contract', 'file-csv',
            'file-download', 'file-excel', 'file-export', 'file-image', 'file-import',
            'file-invoice', 'file-invoice-dollar', 'file-pdf', 'file-powerpoint',
            'file-prescription', 'file-signature', 'file-text', 'file-upload', 'file-video',
            'file-word', 'folder', 'folder-minus', 'folder-open', 'folder-plus', 'paste',
            'save', 'scroll', 'sticky-note'
        ],

        # Interface and controls
        'interface': [
            'adjust', 'bars', 'border-all', 'border-none', 'border-style', 'cog', 'cogs',
            'columns', 'compress', 'desktop', 'download', 'edit', 'ellipsis-h', 'ellipsis-v',
            'expand', 'eye', 'eye-slash', 'filter', 'grip-horizontal', 'grip-lines',
            'grip-lines-vertical', 'grip-vertical', 'home', 'i-cursor', 'list', 'list-alt',
            'list-ol', 'list-ul', 'lock', 'lock-open', 'menu', 'minus', 'minus-circle',
            'minus-square', 'mouse-pointer', 'navicon', 'plus', 'plus-circle', 'plus-square',
            'power-off', 'search', 'search-minus', 'search-plus', 'server', 'settings',
            'sliders', 'sliders-h', 'sort', 'sort-down', 'sort-up', 'table', 'tablet',
            'tablet-alt', 'tasks', 'th', 'th-large', 'th-list', 'times', 'times-circle',
            'toggle-off', 'toggle-on', 'tools', 'trash', 'trash-alt', 'trash-restore',
            'trash-restore-alt', 'unlock', 'unlock-alt', 'upload', 'window-close',
            'window-maximize', 'window-minimize', 'window-restore'
        ],

        # Text and typography
        'text': [
            'align-center', 'align-justify', 'align-left', 'align-right', 'bold', 'font',
            'heading', 'highlighter', 'italic', 'paragraph', 'quote-left', 'quote-right',
            'strikethrough', 'subscript', 'superscript', 'text-height', 'text-width',
            'underline'
        ],

        # Emotions and expressions
        'emotions': [
            'angry', 'dizzy', 'flushed', 'frown', 'frown-open', 'grimace', 'grin', 'grin-alt',
            'grin-beam', 'grin-beam-sweat', 'grin-hearts', 'grin-squint', 'grin-squint-tears',
            'grin-stars', 'grin-tears', 'grin-tongue', 'grin-tongue-squint', 'grin-tongue-wink',
            'grin-wink', 'kiss', 'kiss-beam', 'kiss-wink-heart', 'laugh', 'laugh-beam',
            'laugh-squint', 'laugh-wink', 'meh', 'meh-blank', 'meh-rolling-eyes', 'sad-cry',
            'sad-tear', 'smile', 'smile-beam', 'smile-wink', 'surprise', 'tired'
        ],

        # Transportation
        'transportation': [
            'bicycle', 'bus', 'bus-alt', 'car', 'car-alt', 'car-battery', 'car-crash', 'car-side',
            'motorcycle', 'plane', 'plane-arrival', 'plane-departure', 'ship', 'shuttle-van',
            'subway', 'taxi', 'train', 'tram', 'truck', 'truck-loading', 'truck-monster',
            'truck-moving', 'truck-pickup'
        ],

        # Food and dining
        'food': [
            'apple-alt', 'bacon', 'beer', 'birthday-cake', 'bread-slice', 'candy-cane',
            'carrot', 'cheese', 'cocktail', 'coffee', 'cookie', 'cookie-bite', 'cutlery',
            'drumstick-bite', 'egg', 'hamburger', 'hotdog', 'ice-cream', 'lemon', 'pepper-hot',
            'pizza-slice', 'utensil-spoon', 'utensils', 'wine-bottle', 'wine-glass',
            'wine-glass-alt'
        ],

        # Science and research
        'science': [
            'atom', 'battery', 'battery-empty', 'battery-full', 'battery-half', 'battery-quarter',
            'battery-three-quarters', 'beaker', 'biohazard', 'brain', 'burn', 'calculator',
            'cloud', 'dna', 'fire', 'flask', 'magnet', 'microscope', 'radiation', 'radiation-alt',
            'rainbow', 'seedling', 'solar-panel', 'temperature-high', 'temperature-low'
        ]
    }

    # Default category and keywords
    category = 'misc'
    keywords = []

    # Check for brand icons first (they often have specific patterns)
    if any(brand in icon_name for brand in categories['brand']):
        category = 'brand'
        keywords = ['brand', 'social', 'platform', 'service']

    # Check other categories
    for cat_name, icons in categories.items():
        if icon_name in icons:
            category = cat_name
            break

    # Generate keywords based on icon name
    icon_words = icon_name.replace('-', ' ').split()
    keywords.extend(icon_words)

    # Add category-specific keywords
    category_keywords = {
        'accessibility': ['accessibility', 'disabled', 'universal', 'access'],
        'arrows': ['arrow', 'direction', 'navigation', 'movement'],
        'medical': ['medical', 'health', 'healthcare', 'hospital'],
        'communication': ['communication', 'contact', 'message', 'talk'],
        'files': ['file', 'document', 'storage', 'data'],
        'interface': ['interface', 'ui', 'control', 'button'],
        'text': ['text', 'typography', 'format', 'writing'],
        'emotions': ['emotion', 'face', 'feeling', 'expression'],
        'transportation': ['transport', 'vehicle', 'travel', 'movement'],
        'food': ['food', 'dining', 'meal', 'kitchen'],
        'science': ['science', 'research', 'laboratory', 'technology']
    }

    if category in category_keywords:
        keywords.extend(category_keywords[category])

    # Remove duplicates and clean up
    keywords = list(set(keywords))
    keywords = [k for k in keywords if k and len(k) > 1]

    return category, keywords

def determine_icon_type(icon_name: str) -> str:
    """Determine if icon is solid, regular, or brand based on common patterns"""

    # Brand icons (well-known brand names)
    brand_indicators = [
        'facebook', 'twitter', 'instagram', 'youtube', 'google', 'apple', 'microsoft',
        'amazon', 'github', 'linkedin', 'pinterest', 'reddit', 'snapchat', 'spotify',
        'netflix', 'paypal', 'visa', 'mastercard', 'bitcoin', 'ethereum', 'android',
        'windows', 'adobe', 'angular', 'react', 'vue', 'node', 'npm', 'python', 'php'
    ]

    if any(brand in icon_name for brand in brand_indicators):
        return 'brand'

    # Regular icons (outline versions)
    regular_indicators = ['-o', 'outline', 'regular']
    if any(indicator in icon_name for indicator in regular_indicators):
        return 'regular'

    # Default to solid
    return 'solid'

def get_css_class(icon_name: str, icon_type: str) -> str:
    """Generate appropriate CSS class based on icon type"""
    prefix_map = {
        'solid': 'las',
        'regular': 'lar',
        'brand': 'lab'
    }
    return f"{prefix_map[icon_type]} la-{icon_name}"

def create_display_name(icon_name: str) -> str:
    """Create a human-readable display name from icon name"""
    # Split on hyphens and capitalize each word
    words = icon_name.split('-')
    # Handle special cases
    special_cases = {
        'alt': 'Alternative',
        'o': '',  # Remove 'o' suffix common in outline versions
        'usa': 'USA',
        'usd': 'USD',
        'eur': 'EUR',
        'gbp': 'GBP',
        'jpy': 'JPY',
        'krw': 'KRW',
        'inr': 'INR',
        'cny': 'CNY',
        'btc': 'BTC',
        'eth': 'ETH',
        'id': 'ID',
        'api': 'API',
        'url': 'URL',
        'html': 'HTML',
        'css': 'CSS',
        'js': 'JavaScript',
        'php': 'PHP',
        'sql': 'SQL',
        'xml': 'XML',
        'json': 'JSON',
        'pdf': 'PDF',
        'csv': 'CSV',
        'rss': 'RSS',
        'wifi': 'WiFi',
        'usb': 'USB',
        'gps': 'GPS',
        'sms': 'SMS',
        'ui': 'UI',
        'ux': 'UX',
        'seo': 'SEO',
        'cms': 'CMS',
        'crm': 'CRM',
        'erp': 'ERP',
        'aws': 'AWS',
        'cdn': 'CDN',
        'dns': 'DNS',
        'vpn': 'VPN',
        'ssh': 'SSH',
        'ftp': 'FTP',
        'http': 'HTTP',
        'https': 'HTTPS'
    }

    processed_words = []
    for word in words:
        if word in special_cases:
            if special_cases[word]:  # Don't add empty replacements
                processed_words.append(special_cases[word])
        else:
            processed_words.append(word.capitalize())

    return ' '.join(processed_words)

def build_comprehensive_icon_list():
    """Build the comprehensive icon list from the extracted data"""

    # Read the icon names from file
    with open('/tmp/line-awesome-icons.txt', 'r') as f:
        icon_names = [line.strip() for line in f if line.strip()]

    icons = []
    categories_used = set()

    for icon_name in icon_names:
        if not icon_name:
            continue

        # Determine category and keywords
        category, keywords = categorize_icon(icon_name)
        categories_used.add(category)

        # Determine icon type
        icon_type = determine_icon_type(icon_name)

        # Handle regular/outline versions
        if icon_name.endswith('-o'):
            base_name = icon_name[:-2]
            icons.append({
                "name": base_name,
                "displayName": create_display_name(base_name),
                "cssClass": "lar la-" + base_name,
                "iconType": "regular",
                "category": category,
                "keywords": keywords
            })
            icons.append({
                "name": base_name,
                "displayName": create_display_name(base_name),
                "cssClass": "las la-" + base_name,
                "iconType": "solid",
                "category": category,
                "keywords": keywords
            })
        else:
            icons.append({
                "name": icon_name,
                "displayName": create_display_name(icon_name),
                "cssClass": get_css_class(icon_name, icon_type),
                "iconType": icon_type,
                "category": category,
                "keywords": keywords
            })

    # Define comprehensive category descriptions
    category_descriptions = {
        "accessibility": "Accessibility & Universal Access",
        "achievements": "Awards & Achievements",
        "arrows": "Arrows & Directions",
        "brand": "Brand & Social Media",
        "buildings": "Buildings & Architecture",
        "business": "Business & Commerce",
        "communication": "Communication & Contact",
        "emotions": "Emotions & Expressions",
        "files": "Files & Documents",
        "food": "Food & Dining",
        "geography": "Geography & Maps",
        "household": "Home & Household",
        "interface": "Interface & Controls",
        "maritime": "Maritime & Nautical",
        "medical": "Medical & Health",
        "misc": "Miscellaneous",
        "science": "Science & Research",
        "symbols": "Symbols & Signs",
        "text": "Text & Typography",
        "transportation": "Transportation & Vehicles"
    }

    # Create final structure
    result = {
        "metadata": {
            "version": "1.3.0",
            "totalIcons": len(icons),
            "description": "Line Awesome - Free icon font replacement for Font Awesome",
            "source": "https://icons8.com/line-awesome",
            "license": "MIT",
            "lastUpdated": "2025-09-16",
            "categories": list(categories_used),
            "iconTypes": ["solid", "regular", "brand"]
        },
        "icons": icons,
        "categories": {cat: category_descriptions.get(cat, cat.title()) for cat in sorted(categories_used)}
    }

    return result

if __name__ == "__main__":
    print("Building comprehensive Line Awesome icons JSON...")
    icon_data = build_comprehensive_icon_list()

    # Save to JSON file
    output_file = '/Users/sharifur/Desktop/sharifur/localhost/aiBuilder/line-awesome-icons-complete.json'
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(icon_data, f, indent=2, ensure_ascii=False)

    print(f"✅ Successfully created {output_file}")
    print(f"📊 Total icons: {len(icon_data['icons'])}")
    print(f"📂 Categories: {len(icon_data['categories'])}")
    print(f"🏷️ Icon types: {', '.join(icon_data['metadata']['iconTypes'])}")