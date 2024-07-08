# What is it?
Info about public transport in Gdańsk.
# How does it work?
- Fetches Gdańsk ZTM API every few seconds and updates it's info.
- Fetching API and inserting into MYSQL DB is in Go, the rest is PHP
# Issues
- fetcher.go deletes and inserts instead of updating. Works with zero problems but not the cleanest approach.
- Better connect the database of vechicles, switch to the free TomTom maps api to display posiitons instead of Google maps.
