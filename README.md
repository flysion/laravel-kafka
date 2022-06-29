+ librdkafka => v1.0.1
+ phprdkafka => 5.0.0

# Using
## Consumer
command
> php artisan kafka:HighConsumer

example:

    # Data to file
    
        php artisan kafka:HighConsumer default \
        -C group.id=test \
        --topic=test --logger=null --deserializer=json --ignore-error \
        --processor=file -O file=php://stdout
    
    # Data to job
    
        php artisan kafka:HighConsumer default \
        -C group.id=test \
        --topic=test --logger=stdout --deserializer=json --ignore-error \
        --processor=job -O job=\\App\\Jobs\\Example -O connection=redis
    
    # Data to event
    
        php artisan kafka:HighConsumer default \
        -C group.id=test \
        --topic=test --logger=stdout --deserializer=json --ignore-error \
        --processor=event

    # Data to multi
        
        php artisan kafka:HighConsumer default \
        -C group.id=test \
        --topic=test --logger=stdout --deserializer=json --ignore-error \
        --processor=file -O file=php://stdout \
        --processor=job -O job=\\App\\Jobs\\Example -O connection=redis
