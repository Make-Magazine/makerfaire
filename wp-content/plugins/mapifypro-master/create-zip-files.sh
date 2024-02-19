#!/bin/bash

if [ -t 1 ]; then
  set -e
  boldtext=$(tput bold)
  normaltext=$(tput sgr0)
  greentextcolor="\e[32m"
  redtextcolor="\e[31m"
  normaltextcolor="\e[0m"
fi

createZipFiles () { 
	echo -e "${boldtext}${greentextcolor}# Removing the _dist folder${normaltextcolor}${normaltext}"
	
	rm -rf _dist/
	mkdir -p _dist/mapifypro-cleaned

	echo -e "${boldtext}${greentextcolor}# Creating a clean plugin folder${normaltextcolor}${normaltext}"

	rsync -a --info=progress2 --no-inc-recursive --quiet --exclude='.gitlab-ci.yml' --exclude='build.sh' --exclude='_dist/' --exclude='.git/' --exclude='node_modules/' --exclude='modules/prettyroutes/node_modules/' * _dist/mapifypro-cleaned/

	sed -i "s/\(define('MAPIFY_AM_PRODUCT_ID', \)[0-9]\+/\18672/" _dist/mapifypro-cleaned/mpfy-api-manager-product-id.php

    echo ""


    mkdir -p _dist/mapifypro-master _dist/mapifypro_dev_master _dist/mapifypro_s_yearly_master _dist/mapifypro_d_yearly_master _dist/mapifypro_test
	echo -e "${boldtext}${greentextcolor}# Creating a plugin folder for mapifypro-master${normaltextcolor}${normaltext}"
    cp -R _dist/mapifypro-cleaned/* ./_dist/mapifypro-master/ || echo ""
    sed -i 's/8672/8672/g' _dist/mapifypro-master/mpfy-api-manager-product-id.php

	echo -e "${boldtext}${greentextcolor}# Creating a plugin folder for mapifypro_dev_master${normaltextcolor}${normaltext}"
    cp -R _dist/mapifypro-cleaned/* ./_dist/mapifypro_dev_master/ || echo ""
    sed -i 's/8672/8739/g' _dist/mapifypro_dev_master/mpfy-api-manager-product-id.php

	echo -e "${boldtext}${greentextcolor}# Creating a plugin folder for mapifypro_s_yearly_master${normaltextcolor}${normaltext}"
    cp -R _dist/mapifypro-cleaned/* ./_dist/mapifypro_s_yearly_master/ || echo ""
    sed -i 's/8672/46018/g' _dist/mapifypro_s_yearly_master/mpfy-api-manager-product-id.php

	echo -e "${boldtext}${greentextcolor}# Creating a plugin folder for mapifypro_d_yearly_master${normaltextcolor}${normaltext}"
    cp -R _dist/mapifypro-cleaned/* ./_dist/mapifypro_d_yearly_master/ || echo ""
    sed -i 's/8672/46019/g' _dist/mapifypro_d_yearly_master/mpfy-api-manager-product-id.php

	echo -e "${boldtext}${greentextcolor}# Creating a plugin folder for mapifypro_test${normaltextcolor}${normaltext}"
    cp -R _dist/mapifypro-cleaned/* ./_dist/mapifypro_test/ || echo ""
    sed -i 's/8672/111026/g' _dist/mapifypro_test/mpfy-api-manager-product-id.php


    echo ""

    cd _dist
	echo -e "${boldtext}${greentextcolor}# Creating a zip file for mapifypro-master${normaltextcolor}${normaltext}"
    zip -9 -q -r mapifypro-master mapifypro-master

	echo -e "${boldtext}${greentextcolor}# Creating a zip file for mapifypro_dev_master${normaltextcolor}${normaltext}"
    zip -9 -q -r mapifypro_dev_master mapifypro_dev_master

	echo -e "${boldtext}${greentextcolor}# Creating a zip file for mapifypro_s_yearly_master${normaltextcolor}${normaltext}"
    zip -9 -q -r mapifypro_s_yearly_master mapifypro_s_yearly_master

	echo -e "${boldtext}${greentextcolor}# Creating a zip file for mapifypro_d_yearly_master${normaltextcolor}${normaltext}"
    zip -9 -q -r mapifypro_d_yearly_master mapifypro_d_yearly_master

	echo -e "${boldtext}${greentextcolor}# Creating a zip file for mapifypro_test${normaltextcolor}${normaltext}"
    zip -9 -q -r mapifypro_test mapifypro_test

	echo -e "${boldtext}${greentextcolor}# Cleaning up${normaltextcolor}${normaltext}"

    rm -rf mapifypro-master
    rm -rf mapifypro_dev_master
    rm -rf mapifypro_s_yearly_master
    rm -rf mapifypro_d_yearly_master
    rm -rf mapifypro_test
    rm -rf mapifypro-cleaned

	echo -e "${boldtext}${greentextcolor}# Done${normaltextcolor}${normaltext}"
}

createZipFiles