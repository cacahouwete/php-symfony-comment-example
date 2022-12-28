#!/bin/bash

uid=$(stat -c %u /srv)
gid=$(stat -c %g /srv)

if [ "$uid" == 0 ] && [ "$gid" == 0 ]; then
    if [ $# -eq 0 ]; then
        chown -R foo:bar /srv/var
        supervisord
    else
        exec "$@"
    fi
fi

sed -i -r "s/foo:x:\d+:\d+:/foo:x:$uid:$gid:/g" /etc/passwd
sed -i -r "s/bar:x:\d+:/bar:x:$gid:/g" /etc/group

user=$(grep ":x:$uid:" /etc/passwd | cut -d: -f1)
chown "$user":bar /home
chown -R "$user":bar /srv/var

if [ $# -eq 0 ]; then
    supervisord
else
    exec gosu "$user" "$@"
fi
