#!/bin/bash
set -e

commands=(
	"composer install"
	"composer normalize --dry-run --indent-size=1 --indent-style=tab"
	"vendor/bin/php-cs-fixer fix --diff --dry-run --verbose"
	#"vendor/bin/phpmd src xml phpmd.xml"
	"vendor/bin/phpstan analyse -vvv"
	"vendor/bin/phpunit"
	"phpdoc"
)

color_default=$(tput sgr0)
color_green=$(tput setaf 2)
color_red=$(tput setaf 1)

for command in "${commands[@]}"; do
	echo "${color_green}$ ${command}${color_default}"
	if ! eval "${command}"; then
		echo "${color_red}ERROR: Test failed${color_default}"
		exit
	fi
done

echo
echo "${color_green}Test succeeded${color_default}"
