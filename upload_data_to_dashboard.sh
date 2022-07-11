#!/bin/sh
if [ "$#" -le 1 ]; then
  echo "Usage: $0 DIRECTORY, URL, LAST_DATE, TIME(S) BEETWEEN 2 EMISSIONS " >&2
  exit 1
fi
if ! [ -e "$1" ]; then
  echo "$1 not found" >&2
  exit 1
fi
typeFile=0
if [ -f "$1" ]; then
  typeFile=1
elif ! [ -d "$1" ]; then
  echo "$1 not a directory" >&2
  exit 1
fi

dir=$1
url=$2

if [ -e "./lastDate" ]; then
    lastDate=`cat lastDate`
else
    lastDate="2020-01-23 11h30m43"
fi

dtime=60

if [ "$#" -ge 3 ]; then
  lastDate=$3
fi

if [ "$#" -ge 4 ]; then
  dtime=$4
fi
if [ $typeFile = 0 ]; then
  echo "Scanning json files ..."
  while true; do
      localMaxDate=$lastDate
      for i in $1/*.json; do
          [ -f "$i" ] || break
          if [[ ${i} != *"day"* ]];then
              len=${#i}
              start=$(($len-23))
              stop=$(($len-5))
              dateFile=$(echo $i| cut -c $start-$stop)
              if [ "$dateFile" \> "$localMaxDate" ]; then
                  echo $dateFile
                  echo $localMaxDate
                  localMaxDate=$dateFile
                  echo "sending json"
                  curl -X POST -H "Content-Type: application/json" -d @"$i" "$2"
              fi
          fi
          
      done
      lastDate=$localMaxDate
      echo $localMaxDate > lastDate
      echo "Waiting $dtime seconds"
      sleep $dtime
  done
else
    echo "sending json"
    curl -X POST -H "Content-Type: application/json" -d @"$1" "$2"
    echo "done"
fi
