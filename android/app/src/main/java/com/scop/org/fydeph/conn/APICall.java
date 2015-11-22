package com.scop.org.fydeph.conn;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;

import java.net.HttpURLConnection;
import java.net.URL;

import org.json.JSONException;
import org.json.JSONObject;

import android.os.AsyncTask;
import android.widget.Toast;

public class APICall extends AsyncTask<String, Void, String> {

	private final String APIROOT = "http://fydeph-scop.rhcloud.com/api/1.0/";
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

			URL url = new URL(APIROOT+params[0]);
			HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
			InputStream is = new BufferedInputStream(urlConnection.getInputStream());

        	BufferedReader reader = new BufferedReader(new InputStreamReader(is, "iso-8859-1"), 8);
        	StringBuilder sb = new StringBuilder();
        	String line = null;
        	while ((line = reader.readLine()) != null) // Read line by line
				sb.append(line+"\n");

        	String resString = sb.toString(); // Result is here
        	is.close(); // Close the stream
			urlConnection.disconnect();
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
