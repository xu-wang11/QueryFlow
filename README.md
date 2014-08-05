#QueryFlow#
version:0.1
##Description##
QueryFlow is a restful webservice. This api connect with floodlight to query flow information and install filter.
##Useage##
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

