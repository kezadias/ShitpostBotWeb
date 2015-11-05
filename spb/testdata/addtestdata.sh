#!/bin/sh
#Use thic script only on a empty database as rowids in template_images correspond to templates id field
sqlite3 ../database/sqlite3.db < ../scripts/tables.sql
sqlite3 ../database/sqlite3.db < testdata.sql
