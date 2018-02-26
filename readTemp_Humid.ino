class LM335
{
  float cal;
  int pin;
  public:
  LM335(float mCal, int mPin);
  float measureV();
  float measureK();
  float measureC();
  float measureF();
  float measureRankine();
};

LM335::LM335(float mCal, int mPin)
{
  cal = mCal;
  pin = mPin;
}
float LM335::measureV()
{
  float retVal = (float) analogRead(pin);
  retVal = (retVal*cal)/1024.0;
  return retVal;
}
float LM335::measureK()
{
  return measureV()/0.01;//10mV/k
}
float LM335::measureC()
{
  return (measureV()/0.01)-273.15;
}
float LM335::measureF()
{
  return (((measureV()/0.01)-273.15)*1.8)+32;
}
float LM335::measureRankine()
{
  return measureF() + 458.67;
}
LM335 mTemp(4.92, 0);//supply volts, analog pin
int HIH4000_Pin = A1; //analog pin 1


volatile int seconds = 0;
float temperature;
float humidity;
float dewPoint;

ISR(TIMER1_COMPA_vect)
{
	seconds++;
}

void setup() 
{
    Serial.begin(9600);

	TCCR1A = 0;     // set entire TCCR1A register to 0
	TCCR1B = 0;     // same for TCCR1B
					// set compare match register to desired timer count:
	OCR1A = 15625;  // F_CPU / 1024

	// turn on CTC mode:
	TCCR1B |= (1 << WGM12);

	// Set CS10 and CS12 bits for 1024 prescaler:
	TCCR1B |= (1 << CS10);
	TCCR1B |= (1 << CS12);

	// enable timer compare interrupt:
	TIMSK1 |= (1 << OCIE1A);
}

double computeDewPoint2(double celsius, double humidity)
{
        double RATIO = 373.15 / (273.15 + celsius);  // RATIO was originally named A0, possibly confusing in Arduino context
        double SUM = -7.90298 * (RATIO - 1);
        SUM += 5.02808 * log10(RATIO);
        SUM += -1.3816e-7 * (pow(10, (11.344 * (1 - 1/RATIO ))) - 1) ;
        SUM += 8.1328e-3 * (pow(10, (-3.49149 * (RATIO - 1))) - 1) ;
        SUM += log10(1013.246);
        double VP = pow(10, SUM - 3) * humidity;
        double T = log(VP/0.61078);   // temp var
        return (241.88 * T) / (17.558 - T);
}

void readSensors()
{
  float relativeHumidity  = analogRead(HIH4000_Pin);

  float av=0.0048875*relativeHumidity;
 
  float res=(av-0.86)/0.03;

  dewPoint = computeDewPoint2(mTemp.measureC(), res);
	temperature = mTemp.measureC();
	humidity = res;
}

void loop() 
{
    
	if(seconds >= 5)
	{
		readSensors();

		Serial.print("x");
		Serial.print(";");
		Serial.print(temperature);
		Serial.print(";");
		Serial.print(humidity);
		Serial.print(";");
		Serial.println(dewPoint);
		
		seconds = 0;
	}
} 
