while true
do
status="$(curl -Is http://localhost:8080 | head -1)"
validate=( $status )
echo $status
if [ ${validate[-2]} == "200" ]; then
  echo "OK"
  timeToWait=30
else
  echo "NOT RESPONDING"
  ssh -X -L 8080:localhost:80 root@sshserver.bluecogroup.com -p 60062
  timeToWait=10
fi
echo "Waiting $timeToWait s ..."
sleep $timeToWait;
done
