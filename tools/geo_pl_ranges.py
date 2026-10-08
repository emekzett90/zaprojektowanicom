#!/usr/bin/env python3
"""Builds the Poland IP ranges used by the geo language redirect (zaprojektowani-languages/includes/Geo.php).

Source: the "user-country" database of sapics/ip-location-db (PDDL 1.0, public domain, updated daily):
  https://github.com/sapics/ip-location-db/releases/download/latest/user-country-ipv4-num.csv
  https://github.com/sapics/ip-location-db/releases/download/latest/user-country-ipv6-num.csv

Run from the repository root:  python3 tools/geo_pl_ranges.py <ipv4-num.csv> <ipv6-num.csv>
Writes zaprojektowani-suite/zaprojektowani-languages/data/geo/pl-v4.bin (8 bytes per range: start, end as
big-endian uint32), pl-v6.bin (32 bytes per range: start, end as 16-byte big-endian) and meta.php, all sorted
and merged, so PHP can binary-search them without loading anything else.
"""
import csv
import datetime
import pathlib
import sys

out = pathlib.Path(__file__).resolve().parent.parent / 'zaprojektowani-suite' / 'zaprojektowani-languages' / 'data' / 'geo'


def ranges(path):
    rows = sorted((int(a), int(b)) for a, b, c in csv.reader(open(path, encoding='utf-8')) if c == 'PL')
    merged = []
    for a, b in rows:
        if merged and a <= merged[-1][1] + 1:
            merged[-1] = (merged[-1][0], max(merged[-1][1], b))
        else:
            merged.append((a, b))
    return merged


v4 = ranges(sys.argv[1])
v6 = ranges(sys.argv[2])
out.mkdir(parents=True, exist_ok=True)
(out / 'pl-v4.bin').write_bytes(b''.join(a.to_bytes(4, 'big') + b.to_bytes(4, 'big') for a, b in v4))
(out / 'pl-v6.bin').write_bytes(b''.join(a.to_bytes(16, 'big') + b.to_bytes(16, 'big') for a, b in v6))
today = datetime.date.today().isoformat()
(out / 'meta.php').write_text(
    "<?php\n// Poland IP ranges for the geo language redirect: tools/geo_pl_ranges.py, sapics/ip-location-db user-country (PDDL 1.0).\n"
    f"return ['built' => '{today}', 'v4' => {len(v4)}, 'v6' => {len(v6)}];\n",
    encoding='utf-8',
)
print(f'PL: {len(v4)} IPv4 ranges, {len(v6)} IPv6 ranges -> {out}')
