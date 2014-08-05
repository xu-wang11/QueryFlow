#QueryFlow#
version:0.1
##Description##
QueryFlow is a restful webservice. This api connect with floodlight to query flow information and install filter.
##Useage##
###Init Database###
- create a mysql database, for example:senss
- execute the schema:
<pre class='hightlight'>
<code class='language-sql'>CREATE TABLE oauth_clients (client_id VARCHAR(80) NOT NULL, client_secret VARCHAR(80) NOT NULL, redirect_uri VARCHAR(2000) NOT NULL, grant_types VARCHAR(80), scope VARCHAR(100), user_id VARCHAR(80), CONSTRAINT clients_client_id_pk PRIMARY KEY (client_id));
CREATE TABLE oauth_access_tokens (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT access_token_pk PRIMARY KEY (access_token));
CREATE TABLE oauth_authorization_codes (authorization_code VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), redirect_uri VARCHAR(2000), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT auth_code_pk PRIMARY KEY (authorization_code));
CREATE TABLE oauth_refresh_tokens (refresh_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT refresh_token_pk PRIMARY KEY (refresh_token));
CREATE TABLE oauth_users (username VARCHAR(255) NOT NULL, password VARCHAR(2000), first_name VARCHAR(255), last_name VARCHAR(255), CONSTRAINT username_pk PRIMARY KEY (username));
CREATE TABLE oauth_scopes (scope TEXT, is_default BOOLEAN);
CREATE TABLE oauth_jwt (client_id VARCHAR(80) NOT NULL, subject VARCHAR(80), public_key VARCHAR(2000), CONSTRAINT jwt_client_id_pk PRIMARY KEY (client_id));</code>
</pre>
- change the code in Auth/Server.php, $dsn, $username, $password
###Fetch Token###
- <code>curl http://localhost/QueryFlow/token -d "grant_type=client_credentials&&client_id=demoapp&&client_secret=demopass"</code>

###API Interface###
- http://localhost/QueryFlow/getSwitch?access_token=XXX
- http://localhost/QueryFlow/addflow/srcip/srcport/dstip/dstport?access_token== XXX
- http://localhost/QueryFlow/devices?access_token=XXX
- http://localhost/QueryFlow/filter/enable_or_disable/protocol/srcip/dstip?access_token== XXX
- http://localhost/QueryFlow/deleterule/srcip/dstip?access_token== XXX
- http://localhost/QueryFlow/query/srcip/srcport/dstip/dstport?access_token== XXX

##Info##
-  <a href="http://xu-wang11.github.io">Xu Wang</a> xu-wang11@mails.thu.edu.cn

