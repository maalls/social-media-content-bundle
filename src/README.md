To download insipina repo use the following token:
```
e885b99dd431fe5b973f69655575e7101c92f684
```

Add the routes

```
smc_annotations:
    # loads routes from the PHP annotations of the controllers found in that directory
    resource: '../Maalls/SocialMediaContentBundle/Controller/'
    type:     annotation
```

Create a systemd service for each command, stream, rest, pool and schedule
```
in /etc/systemd/system/counter-stream.service

[Unit]
Description=Counter Stream

[Service]
Type=simple
Restart=on-failure
RestartSec=10s
ExecStart=/usr/bin/php /var/www/counter/bin/console smc:twitter:stream
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=counter-stream
```

Configure the logs to be redirected to specific files in /etc/rsyslog.d/counter.conf:

```
if $programname == 'counter-stream' then /var/www/bot/var/log/stream.log
if $programname == 'counter-stream' then ~

```

Restart the syslog:
```
sudo service rsyslog restart
```

start the task

```
systemctl start counter-stream
```
