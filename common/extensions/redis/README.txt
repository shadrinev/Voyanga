The Predis directory was optained from the link below

https://github.com/nrk/predis/tree/v0.7.2

You might replace Predis directory it with more recent one from 

the old single file Predis.php was generated using 

php ./bin/create-single-file.php

CRedisCache.php Code was modified to support autoloading of Predis from multiple files
in other words we are not using the single file

to use the single file you may edit getRedis() in CRedisCache.php

  -- Muayyad Alsadi - 2012-05-27

