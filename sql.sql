# all
select DISTINCT id, email, name, last_login_at, age, distance
from (
    (
        select *, (YEAR(CURRENT_TIMESTAMP) - YEAR(birthdate)) as age,
        (6371.008 *(acos(cos(radians(54)) * cos(radians(`lat`)) * cos(radians(`long`) - radians(73)) + sin(radians(54)) * sin(radians(`lat`))))) as distance,
        (1) as priority
        from users
        having distance < 50
        ORDER BY last_login_at desc
    ) UNION DISTINCT
    (
        select *, (YEAR(CURRENT_TIMESTAMP) - YEAR(birthdate)) as age,
        (6371.008 *(acos(cos(radians(54)) * cos(radians(`lat`)) * cos(radians(`long`) - radians(73)) + sin(radians(54)) * sin(radians(`lat`))))) as distance,
        (2) as priority
        from users
        where country_code='RU'
        ORDER BY last_login_at desc
	) UNION DISTINCT (
        select *, (YEAR(CURRENT_TIMESTAMP) - YEAR(birthdate)) as age,
        (6371.008 *(acos(cos(radians(54)) * cos(radians(`lat`)) * cos(radians(`long`) - radians(73)) + sin(radians(54)) * sin(radians(`lat`))))) as distance,
        (3) as priority
        from users
        ORDER BY last_login_at desc
    )
) as t1
having
age between 0 and 999
ORDER BY
    t1.priority ASC,
	last_login_at DESC
LIMIT 0,1000