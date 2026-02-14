#!/usr/bin/env sh
# create /opt/phpstorm-coverage if it doesn't exist
# make sure owned by sail user
COVERAGE_DIR="/opt/phpstorm-coverage"
if [ ! -d "$COVERAGE_DIR" ]; then
    # create /opt/ (and other folders) as root
    mkdir -p "$COVERAGE_DIR"
    # change ownership of the opt dir and coverage dir to sail user
    chown -R sail:sail "/opt/"
fi

# https://intellij-support.jetbrains.com/hc/en-us/community/posts/7236436079762-Docker-PHPStorm-Laravel-Sail-Connect-to-container-as-sail
runuser -u sail -- php "$@"
