"""
Build site/state-shapes.js: one outline per state plus DC, for the vote card generator.

Source: us-atlas 3.0.1 states-10m.json (US Census Bureau cartographic boundaries, public domain),
saved beside this script. Each state gets its own projection (longitude scaled by the cosine of
its middle latitude), so no state is squashed the way a national Albers map squashes Maine or
Washington. Small islands are dropped; the outline is simplified and scaled so the longer side is
1000 units. The capital is projected the same way and drawn as the star.

Run: python3 build_shapes.py   (no third-party packages)
"""
import json
import math
from pathlib import Path

HERE = Path(__file__).resolve().parent
SRC = HERE / "us-atlas-states-10m.json"
OUT = HERE.parent / "site" / "state-shapes.js"

FIPS = {
    "01": "AL", "02": "AK", "04": "AZ", "05": "AR", "06": "CA", "08": "CO", "09": "CT", "10": "DE",
    "11": "DC", "12": "FL", "13": "GA", "15": "HI", "16": "ID", "17": "IL", "18": "IN", "19": "IA",
    "20": "KS", "21": "KY", "22": "LA", "23": "ME", "24": "MD", "25": "MA", "26": "MI", "27": "MN",
    "28": "MS", "29": "MO", "30": "MT", "31": "NE", "32": "NV", "33": "NH", "34": "NJ", "35": "NM",
    "36": "NY", "37": "NC", "38": "ND", "39": "OH", "40": "OK", "41": "OR", "42": "PA", "44": "RI",
    "45": "SC", "46": "SD", "47": "TN", "48": "TX", "49": "UT", "50": "VT", "51": "VA", "53": "WA",
    "54": "WV", "55": "WI", "56": "WY",
}

# Capitals, (latitude, longitude). DC uses the city center.
CAPITALS = {
    "AL": (32.377, -86.300), "AK": (58.302, -134.420), "AZ": (33.448, -112.074),
    "AR": (34.746, -92.289), "CA": (38.576, -121.494), "CO": (39.739, -104.985),
    "CT": (41.764, -72.682), "DE": (39.157, -75.520), "DC": (38.900, -77.020),
    "FL": (30.438, -84.281), "GA": (33.749, -84.388), "HI": (21.307, -157.858),
    "ID": (43.615, -116.202), "IL": (39.798, -89.654), "IN": (39.768, -86.158),
    "IA": (41.591, -93.604), "KS": (39.048, -95.678), "KY": (38.187, -84.875),
    "LA": (30.457, -91.187), "ME": (44.307, -69.782), "MD": (38.979, -76.491),
    "MA": (42.358, -71.064), "MI": (42.734, -84.555), "MN": (44.955, -93.102),
    "MS": (32.304, -90.182), "MO": (38.579, -92.173), "MT": (46.586, -112.018),
    "NE": (40.808, -96.700), "NV": (39.164, -119.766), "NH": (43.207, -71.538),
    "NJ": (40.220, -74.770), "NM": (35.682, -105.940), "NY": (42.652, -73.757),
    "NC": (35.780, -78.639), "ND": (46.821, -100.783), "OH": (39.961, -82.999),
    "OK": (35.492, -97.503), "OR": (44.938, -123.030), "PA": (40.264, -76.884),
    "RI": (41.831, -71.415), "SC": (34.000, -81.033), "SD": (44.367, -100.346),
    "TN": (36.166, -86.784), "TX": (30.275, -97.740), "UT": (40.777, -111.888),
    "VT": (44.262, -72.581), "VA": (37.539, -77.434), "WA": (47.036, -122.905),
    "WV": (38.336, -81.612), "WI": (43.075, -89.384), "WY": (41.140, -104.820),
}

# Keep an island when its area is at least this share of the state's largest piece.
KEEP_SHARE = {"HI": 0.004, "AK": 0.01}
KEEP_DEFAULT = 0.02
TOLERANCE = 1.1  # simplification, in output units (longer side = 1000)


def decode_arcs(topo):
    sx, sy = topo["transform"]["scale"]
    tx, ty = topo["transform"]["translate"]
    arcs = []
    for arc in topo["arcs"]:
        x = y = 0
        pts = []
        for dx, dy in arc:
            x += dx
            y += dy
            pts.append((x * sx + tx, y * sy + ty))
        arcs.append(pts)
    return arcs


def ring(arcs, idxs):
    pts = []
    for i in idxs:
        a = arcs[i] if i >= 0 else arcs[~i][::-1]
        pts.extend(a[1:] if pts else a)
    return pts


def area(pts):
    s = 0.0
    for (x1, y1), (x2, y2) in zip(pts, pts[1:] + pts[:1]):
        s += x1 * y2 - x2 * y1
    return abs(s) / 2


def simplify(pts, tol):
    """Douglas-Peucker, iterative."""
    if len(pts) < 4:
        return pts
    keep = [False] * len(pts)
    keep[0] = keep[-1] = True
    stack = [(0, len(pts) - 1)]
    while stack:
        a, b = stack.pop()
        (x1, y1), (x2, y2) = pts[a], pts[b]
        dx, dy = x2 - x1, y2 - y1
        norm = math.hypot(dx, dy) or 1e-9
        best, at = 0.0, -1
        for i in range(a + 1, b):
            x0, y0 = pts[i]
            d = abs(dy * x0 - dx * y0 + x2 * y1 - y2 * x1) / norm
            if d > best:
                best, at = d, i
        if best > tol and at > 0:
            keep[at] = True
            stack.append((a, at))
            stack.append((at, b))
    return [p for p, k in zip(pts, keep) if k]


def main():
    topo = json.loads(SRC.read_text())
    arcs = decode_arcs(topo)
    shapes = {}
    for g in topo["objects"]["states"]["geometries"]:
        abbr = FIPS.get(g.get("id"))
        if not abbr:
            continue  # territories
        polys = g["arcs"] if g["type"] == "MultiPolygon" else [g["arcs"]]
        rings = []
        for poly in polys:
            outer = ring(arcs, poly[0])  # holes are dropped; no state outline needs one
            if abbr == "AK":
                outer = [(lon - 360 if lon > 0 else lon, lat) for lon, lat in outer]
            rings.append(outer)
        biggest = max(area(r) for r in rings)
        share = KEEP_SHARE.get(abbr, KEEP_DEFAULT)
        rings = [r for r in rings if area(r) >= biggest * share]

        lats = [p[1] for r in rings for p in r]
        k = math.cos(math.radians((min(lats) + max(lats)) / 2))
        proj = [[(lon * k, -lat) for lon, lat in r] for r in rings]
        xs = [p[0] for r in proj for p in r]
        ys = [p[1] for r in proj for p in r]
        x0, y0 = min(xs), min(ys)
        scale = 1000 / max(max(xs) - x0, max(ys) - y0)
        w = round((max(xs) - x0) * scale)
        h = round((max(ys) - y0) * scale)

        parts = []
        for r in proj:
            pts = [((x - x0) * scale, (y - y0) * scale) for x, y in r]
            # A closed ring starts and ends on one point, which Douglas-Peucker reads as a zero-length
            # line; split it at the point farthest from the start and simplify each half.
            far = max(range(len(pts)), key=lambda i: math.dist(pts[0], pts[i]))
            pts = simplify(pts[: far + 1], TOLERANCE)[:-1] + simplify(pts[far:], TOLERANCE)
            if len(pts) < 4:
                continue
            parts.append("M" + " ".join(f"{round(x)},{round(y)}" for x, y in pts) + "Z")

        lat, lon = CAPITALS[abbr]
        cap = [round((lon * k - x0) * scale), round((-lat - y0) * scale)]
        shapes[abbr] = {"w": w, "h": h, "d": "".join(parts), "cap": cap}

    body = json.dumps(shapes, separators=(",", ":"), sort_keys=True)
    OUT.write_text(
        "/* State outlines for the vote cards. Generated by build/build_shapes.py from us-atlas 3.0.1\n"
        " * (US Census Bureau cartographic boundaries, public domain). Do not edit by hand. */\n"
        f"window.VOTE_SHAPES={body};\n"
    )
    print(f"{len(shapes)} shapes, {OUT.stat().st_size // 1024} KB -> {OUT}")


if __name__ == "__main__":
    main()
