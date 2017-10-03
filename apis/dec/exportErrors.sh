#!/bin/bash
mongoexport --db dectest --collection error --type=csv --fields _id,tipo,modelo,errCod,errNombre,errDescripcion,errCampo  --out errors.csv
