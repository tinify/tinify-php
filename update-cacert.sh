#!/usr/bin/env bash
dir=lib/data

# Leave out the eMudhra roots.
exclude='emSign|eMudhra'

cert=0
skip=0
label=
curl --silent --fail https://curl.se/ca/cacert.pem  | while read -r line; do
  if [[ "$line" =~ ^=+$ ]]; then
      if [[ "$label" =~ $exclude ]]; then skip=1; else skip=0; fi
  elif [ "-----BEGIN CERTIFICATE-----" == "$line" ]; then
      cert=1
      [ $skip == 1 ] || echo "$line"
  elif [ "-----END CERTIFICATE-----" == "$line" ]; then
      cert=0
      [ $skip == 1 ] || echo "$line"
  else
      if [ $cert == 1 ] && [ $skip == 0 ]; then
          echo "$line"
      fi
  fi
  label=$line
done > "$dir/cacert.pem"
