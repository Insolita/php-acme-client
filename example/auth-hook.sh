#!/usr/bin/env bash

/usr/local/bin/route53 change-resource-record-sets -d "${RR_NAME}" UPSERT _acme-challenge 60 TXT -- "${RR_VALUE}"

EXIT_STATUS=1

LOOKUP_MAX_ATTEMPTS=18
LOOKUP_INTERVAL_SEC=5

ATTEMPTS=0

while [ $ATTEMPTS -lt $LOOKUP_MAX_ATTEMPTS ]
do
    ATTEMPTS=$(( ATTEMPTS + 1 ))

    VALUE=`/usr/local/bin/route53 test-dns-answer -d "${RR_NAME}" "_acme-challenge.${RR_NAME}" TXT | jq -r '.RecordData[] | select(. == "\"'${RR_VALUE}'\"")'`

    if [ '"'${RR_VALUE}'"' = "${VALUE}" ]; then
        sleep 5
        EXIT_STATUS=0
        break
    fi

    /bin/sleep $LOOKUP_INTERVAL_SEC
done

if [ $EXIT_STATUS -eq 1 ]; then
    echo "Cannot lookup the resource record ${RR_NAME}" >&2
fi

exit $EXIT_STATUS
