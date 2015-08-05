<?php

while(1) {
   system('php artisan rsync:stock DGZQ');//rsync order
   system('php artisan trade:calculate');//calculate
   //system('php /data/www/portal/sns/artisan stock:updatecolse');//update yesterday price and amount
   sleep(10);
}


