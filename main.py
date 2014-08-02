__author__ = 'Xu Wang'
import urllib2
import json


#query flow from ASes
def query(localip, controllers):
    array = {}
    for item in controllers:
        if item[-1] != '/':
            item += "/"
        url = item + "query/*/*" + localip + "/*/"
        response = urllib2.urlopen(url)
        result = json.loads(response)
        response.close()
        array[item] = result
    return array


#def install filter
'''
bad_array structure:
(srcip, dstip, protocol)
'''


def install_filter(controllers, bad_array, enable_or_disable=True):
    if enable_or_disable:
        flag = "enable"
    else:
        flag = "disable"
    for controller in controllers:
        for item in bad_array:
            if controller[-1] != '/':
                item += "/"

            url = controller + "filter/" + flag + "/" + item['protocol'] + "/" + item['srcport'] + "/" + item['dstport'] + "/"
            response = urllib2.urlopen(url)
            response.close()





