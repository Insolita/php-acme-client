#!/usr/bin/env bash

/usr/local/bin/route53 change-resource-record-sets -d "${RR_NAME}" DELETE _acme-challenge 60 TXT -- "${RR_VALUE}"
