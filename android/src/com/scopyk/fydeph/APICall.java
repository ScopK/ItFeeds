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

import android.app.Activity;
import android.os.AsyncTask;
import android.widget.Toast;

public class APICall extends AsyncTask<String, Void, String> {
	
	private final String APIROOT = "http://192.168.1.7:8082/api/1.0/";
    private Exception exception;
    private APICallback callback;
    
    public APICall(APICallback c){
    	super();
    	callback=c;
    }

    protected String doInBackground(String... url) {
        try {       	
        	HttpClient httpclient = new DefaultHttpClient(); // Create HTTP Client
        	HttpGet httpget = new HttpGet(APIROOT+url[0]); // Set the action you want to do
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
            this.exception = e;
            return null;
        }
    }

    protected void onPostExecute(String feed) {
    	try {
			JSONObject json = new JSONObject(feed);
			callback.APIResponse(json);
			/*
			if (json.has("error")){
				Toast.makeText(callback.getApplicationContext(), R.string.unknown_user, 5).show();
				return;
			}
			switch(action){
				case LOGIN:
					Toast.makeText(callback.getApplicationContext(), (String)json.get("token"), 5).show();	
					break;
				default:break;
			
			}
			*/
		} catch (JSONException e) {
        	Toast.makeText(callback.getApplicationContext(), "JSON ERROR", 5).show();
			e.printStackTrace();
		}
    }
}
