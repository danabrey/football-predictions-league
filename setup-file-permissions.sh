#!/usr/bin/env bash

sudo setfacl -dR -m u:www-data:rwX -m u:$(whoami):rwX app/var
sudo setfacl -R -m u:www-data:rwX -m u:$(whoami):rwX app/var