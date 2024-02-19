CREATE DATABASE IF NOT EXISTS appointment_graphql_api;
CREATE DATABASE IF NOT EXISTS appointment_graphql_api_test;

GRANT ALL PRIVILEGES ON appointment_graphql_api.* TO 'aqa'@'%';
GRANT ALL PRIVILEGES ON appointment_graphql_api_test.* TO 'aqa'@'%';

FLUSH PRIVILEGES;
