SELECT DISTINCT id, email, name, last_login_at, age, distance, country_code
        FROM ((
                SELECT id, email, name, last_login_at, country_code, (YEAR(CURRENT_TIMESTAMP) - YEAR(birthdate)) as age,
                (6371.008 *(acos(cos(radians('54.2541994')) * cos(radians(`lat`)) * cos(radians(`long`) - radians('73.0866681')) + sin(radians('54.2541994')) * sin(radians(`lat`))))) as distance,
                (3) as priority
                FROM users
            )) as T
        WHERE id<>'14' AND T.age > '18'
                AND EXISTS (
                    SELECT i.user_id
                    FROM user_interests AS i
                    WHERE i.interest_slug = 'interest1'
                    AND i.value = '1'
                    AND i.user_id = T.id
                )

                AND EXISTS (
                    SELECT i.user_id
                    FROM user_interests AS i
                    WHERE i.interest_slug = 'interest2'
                    AND i.value = '0'
                    AND i.user_id = T.id
                )

            AND EXISTS (
                SELECT u.age_limit_min, u.age_limit_max, (YEAR(CURRENT_TIMESTAMP) - YEAR('1998-10-27 10:38:37')) as age
                FROM users AS u
                WHERE u.id = T.id
                HAVING (ISNULL(u.age_limit_min) OR age > u.age_limit_min)
                AND (ISNULL(u.age_limit_max) OR age < u.age_limit_max)
            )

        ORDER BY
        T.priority ASC,
        last_login_at DESC
        LIMIT 0,10