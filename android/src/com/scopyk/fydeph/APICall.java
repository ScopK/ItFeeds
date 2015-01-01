package com.scopyk.fydeph;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONException;
import org.json.JSONObject;

import android.os.AsyncTask;
import android.widget.Toast;

public class APICall extends AsyncTask<String, Void, String> {
	
	private final String APIROOT = "http://192.168.1.7:8082/api/1.0/";
    private APICallback callback;
    private int callId;
    private Object content;
    
    public APICall(APICallback c){
    	super();
    	callback=c;
    }
    
    public APICall(APICallback c, Object content){
    	this(c);
    	this.content=content;
    }

    protected String doInBackground(String... params) {
    	System.out.println("JSONQUERY:"+params[0]);
        try {
        	if (params.length>1)callId = Integer.parseInt(params[1]);
        	else				callId = 0;       	
        	HttpClient httpclient = new DefaultHttpClient(); // Create HTTP Client
        	HttpGet httpget = new HttpGet(APIROOT+params[0]); // Set the action you want to do
        	httpget.setHeader("User-Agent", "Mozilla/5.0 (Linux; U; Android;) FydephApp");
        	HttpResponse response = httpclient.execute(httpget); // Executeit
        	HttpEntity entity = response.getEntity();
        	InputStream is = entity.getContent(); // Create an InputStream with the response
        	BufferedReader reader = new BufferedReader(new InputStreamReader(is, "iso-8859-1"), 8);
        	StringBuilder sb = new StringBuilder();
        	String line = null;
        	while ((line = reader.readLine()) != null) // Read line by line
        	    sb.append(line+"\n");
        	String resString = sb.toString(); // Result is here
        	is.close(); // Close the stream
        	return resString;
        } catch (Exception e) {
            return null;
        }
    }

    protected void onPostExecute(String response) {
    	try {
			JSONObject json = new JSONObject(response);
			callback.APIResponse(json,callId,this);
		} catch (JSONException e) {
        	Toast.makeText(callback.getApplicationContext(), "JSON ERROR", Toast.LENGTH_SHORT).show();
			e.printStackTrace();
		} catch (java.lang.NullPointerException err) {
			String errorResponse="{\"error\":\"No connection\"}";
			if (!errorResponse.equals(response))
				onPostExecute(errorResponse);
		}
    }
    
    public void setContent(Object content){
    	this.content = content;
    }
    
    public Object getContent(){
    	return this.content;
    }
}
