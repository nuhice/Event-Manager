#!/bin/sh
# build.sh

if [ -z "$API_URL" ]; then
  echo "WARNING: API_URL environment variable is not set. Defaulting to placeholder."
else
  echo "Replacing placeholder with $API_URL"
  sed -i "s|__API_URL_PLACEHOLDER__|$API_URL|g" index.html
fi
