#!/bin/bash

MSGMERGE=`which msgmerge`

if [ -z "$MSGMERGE" ]; then
    echo "Error: unable to find msgmerge"
    echo "GNU GetText Utilities must be installed and on the path to run this script"
    exit 1
fi

if [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ]; then
    echo "Usage: translate-updateLanguageFromSuppliedPO.sh new_english_translations.po blogs en"
    echo "Avoids lots of unnecessary messing around with diff/merge"
    exit
fi

if [ ! -e "$1" ] ; then
    echo "Error: File $1 not found";
    exit 1
fi

SCRIPTPATH=$( cd $(dirname $0) ; pwd -P )

TRPATH="${SCRIPTPATH}/../resources/translations/${2}"


if [ ! -e "$TRPATH/$3.po" ]; then
    echo "Error: Existing translation at ${TRPATH}/$3.po does not exist";
    exit 1;
fi

msgmerge -N -o - "$1" "$TRPATH/$3.po" | msgmerge -N -o "$TRPATH/$3.po" - "${TRPATH}/$2.pot"
