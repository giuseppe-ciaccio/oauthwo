bash scripts/set_up.sh clean
bash scripts/set_up.sh create all
chown apache:apache keys/key.pem
chmod 400 keys/key.pem
/bin/rm ../oauth 2>/dev/null
ln -s $PWD/public ../oauth
