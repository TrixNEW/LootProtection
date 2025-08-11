# Loot Protection

A PocketMine-MP 5.x plugin that protects a player's dropped loot from being picked up by others for a configurable duration after they are killed.  
Perfect for PvP servers to prevent loot swiping.

---

## Features
- **Customizable Protection Time** – Set how many seconds loot remains protected for the killer.
- **Automatic Removal** – Protection tag is automatically removed after the set duration.
- **Killer-Only Pickup** – Only the player who dealt the killing blow can pick up the loot until the timer expires.
- **Optimized** – Tested in production with over 100+ players without lag.

---

## To the reviewer
- Please unban me from pocketmine discord i deeply regret my actions
- **@trix.pro**
---

## Installation
1. Download the latest release `.phar` file.
2. Place it into your server's `plugins/` folder.
3. Restart the server.

---

## Configuration
Located in `config.yml`:

```yaml
# The amount of seconds loot will be protected after a player kill
protection-duration: 10
```
---
## How it works
1. When a player is killed, their loot is tagged with:
  - Killer's name
  - Remaining protection time
2. Only the killer can pick up the items during the protection period.
3. After the timer runs out, anyone can pick up the loot.
---
