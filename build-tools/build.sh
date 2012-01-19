trap onexit 1 2 3 15 ERR

function onexit() {
    local exit_status=${1:-$?}
    echo Exiting $0 with $exit_status
    exit $exit_status
}

phing php_tests
phing rebuild
onexit