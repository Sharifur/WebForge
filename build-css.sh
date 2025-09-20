#\!/bin/bash
# Build Tailwind CSS and other assets for production
echo "Building assets..."
npm run build
echo "Assets built successfully\!"

# Optional: Copy to a predictable location for easier reference
# cp public/build/assets/app-*.css public/css/app.css 2>/dev/null || true

echo "Tailwind CSS is now available as a local file instead of CDN"
echo "Check public/build/assets/ for the built files"

