## —— Unit Tests 🎵 ——————————————————————————————————————————————————————————
unit-test:
	vendor/bin/phpunit --configuration phpunit.xml

## —— Code quality 🎵 ——————————————————————————————————————————————————————————
inspect:
	make phpcs ## Code Sniffer
	make phpstan ## Potential bugs
	make phpmd ## Mess Detector

## —— Fix Code quality to standard 🎵 ——————————————————————————————————————————————————————————
fix: phpcbf

phpcs:
	vendor/bin/phpcs --standard=phpcs.xml

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon

phpmd:
	vendor/bin/phpmd Annotations/ Attributes/ Classes/ Command/ Controller/ Entity/ Exceptions/ FieldType/ Form/ Menu/ QueryType/ Repository/ Resources/ Serializer/ Services/ tests/ Validator/ text phpmd.xml

phpcbf:  ## Launch PHP Code Beautiful Fixer to automatically fix code style errors
	vendor/bin/phpcbf --standard=PSR12 --encoding=UTF8 --extensions=php Annotations/ Attributes/ Classes/ Command/ Controller/ Entity/ Exceptions/ FieldType/ Form/ Menu/ QueryType/ Repository/ Resources/ Serializer/ Services/ tests/ Validator/
