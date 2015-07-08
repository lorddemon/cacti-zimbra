# Downloading #

Text will go here


# Cacti Script & Template Install #

Text will go here

# SELinux Policy to Disable SNMP executing #

Determin of SELinux is on for SNMP


# getsebool -a | grep snmpd
# snmpd\_disable\_trans --> off

You will want to turn it on
# setsebool -P snmpd\_disable\_trans 1

Reboot your system to have it take effect

or to temp disable it until the next reboot
# echo 0 > /selinux/enforce