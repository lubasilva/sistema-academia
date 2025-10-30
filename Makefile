up:
	./vendor/bin/sail up -d

down:
	./vendor/bin/sail down

rebuild:
	./vendor/bin/sail build --no-cache

migrate:
	./vendor/bin/sail artisan migrate --force

seed:
	./vendor/bin/sail artisan db:seed

test:
	./vendor/bin/sail artisan test
