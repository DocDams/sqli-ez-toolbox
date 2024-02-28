## —— Unit Tests 🎵 ——————————————————————————————————————————————————————————
unit-test:
	vendor/bin/phpunit --configuration phpunit.xml

## —— Code quality 🎵 ——————————————————————————————————————————————————————————
inspect:
	make phpcs ## Code Sniffer
	make phpstan ## Potential bugs
	make phpmd ## Mess Detector

phpcs:
	vendor/bin/phpcs --standard=phpcs.xml

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon

phpmd:
	vendor/bin/phpmd Annotations/ Attributes/ Classes/ Command/ Controller/ Entity/ Exceptions/ FieldType/ Form/ Menu/ QueryType/ Repository/ Resources/ Serializer/ Services/ tests/ Validator/ text phpmd.xml

