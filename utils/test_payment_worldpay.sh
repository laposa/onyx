#!/bin/bash

#wget --post-data "{POST_DATA}" http://{SERVER}/page/1359?order_id={ORDER_ID}

wget --post-data "testMode=0&authCost=22.69&currency=GBP&address=17+Tetherdown%0D%0AMuswell+Hill&countryString=United+Kingdom&installation=141623&fax=&countryMatch=Y&transId=286433088&AVS=2222&amountString=%26%23163%3B22.69&postcode=N10+1ND&msgType=authResult&name=Mr+Norbert+Laposa&tel=07740+189168&transStatus=Y&desc=Payment+for+goods+from+Jing+Tea.&cardType=Switch&lang=en&transTime=1197631210732&authAmountString=%26%23163%3B22.69&authAmount=22.69&ipAddress=217.196.238.225&cost=22.69&instId=141623&compName=jing+tea+ltd&amount=22.69&country=GB&MC_callback=https%3A%2F%2Fjingtea.com%2Fpage%2F1359%3Forder_id%3D4574&rawAuthMessage=cardbe.msg.authorised&email=mr%40ln5.co.uk&authCurrency=GBP&rawAuthCode=A&cartId=4574&authMode=A" http://{SERVER}/page/1359?order_id=13871
