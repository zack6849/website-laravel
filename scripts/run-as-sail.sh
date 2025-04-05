#!/usr/bin/env sh
# https://intellij-support.jetbrains.com/hc/en-us/community/posts/7236436079762-Docker-PHPStorm-Laravel-Sail-Connect-to-container-as-sail
runuser -u sail -- php "$@"
