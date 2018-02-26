#!/usr/bin/python
#-*-coding: utf-8-*-

import serial, time
import httplib, urllib
import sys
import ftplib
import re
import time
import MySQLdb as mdb

def addDataToMysql(temp,humid,dewPoint):
   # Open database connection
   db = mdb.connect("localhost","pi","my5ql","testDB" )
   cursor = db.cursor()
#    cursor.execute('''INSERT INTO sensorData (date, time, Temperature ,Humidity, Lux)
#                       VALUES (curdate(), curtime(), %s, %s, %s);''',
#                    (temp,humid,lux,))
					
   cursor.execute('''INSERT INTO sensors (temperature, humidity, dewPoint)
                       VALUES (%s, %s, %s);''',
                    (temp,humid,dewPoint))
    # Commit your changes in the database
   db.commit()
   db.close()
	
#start = time.time()
port = serial.Serial('/dev/ttyACM0', 9600, timeout = 2) #portin lukuparametrit: portti, baudinopeus, timeout
port.flushInput()

while True:
    temper = 0
    humid = 0
    dewP = 0
   
    
# silmukka lukee usb-porttiin kytketyltä master-noodilta tulevaa dataa
# kerralla voidaan lukea kuutta noodia yhtä aikaa

    data = port.read(30)

    if data ==[]:
        data = "0;0;0"

    if 'x' in data:

        (val_, val1_, val2_, val3_)= str(data).split(";")

        temper = float(val1_)

        humid = float(val2_)

        dewP = float(val3_)

        print (temper, humid, dewP)
     
        addDataToMysql(temper,humid,dewP)
