## —— Unit Tests 🎵 ——————————————————————————————————————————————————————————
test:
	vendor/bin/phpunit --configuration phpunit.xml

test-suite:
	vendor/bin/phpunit --configuration phpunit.xml --testsuite $(suite)

## —— Code quality 🎵 ——————————————————————————————————————————————————————————
inspect:
	make phpcs ## Code Sniffer
	make phpstan ## Potential bugs
	make phpmd ## Mess Detector
	make rector-dry ## Rector changes

## —— Fix Code quality to standard 🎵 ——————————————————————————————————————————————————————————
fix:
	make rector
	make phpcbf

phpcs:
	vendor/bin/phpcs --standard=phpcs.xml --runtime-set ignore_errors_on_exit 1 --runtime-set ignore_warnings_on_exit 1

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon

phpmd:
	vendor/bin/phpmd ./ text phpmd.xml

phpcbf:  ## Launch PHP Code Beautiful Fixer to automatically fix code style errors
	vendor/bin/phpcbf -p --standard=phpcs.xml --encoding=UTF8 --extensions=php --ignore=vendor/ --runtime-set ignore_errors_on_exit 1 --runtime-set ignore_warnings_on_exit 1 .

rector-dry:
	vendor/bin/rector process --dry-run

rector:
	vendor/bin/rector process