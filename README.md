# What is it?
Info about public transport in Gdańsk.
# How does it work?
- Fetches Gdańsk ZTM API every few seconds and updates it's info.
- Fetching API and inserting into MYSQL DB is in Go, the rest is PHP
# TODO
- Better connect the database of vechicles(foreign keys and stuff)
- switch to the free TomTom maps api to display positons instead of Google maps.
- fetch positions often other stuff like once a day
- handle errors when updating db. e.g when a new vehicle gets added we're gonna see it's position but not in the database of vehicles yet, figure out it got added and we don't have it, update vehicles and insert new ones.