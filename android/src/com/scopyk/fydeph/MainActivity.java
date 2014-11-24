package com.scopyk.fydeph;

import java.io.BufferedReader;
import java.io.IOException;
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
import android.content.Context;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;


public class MainActivity extends Activity implements APICallback {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        loginSetUp();
    }
    
    public void loginSetUp(){
        final APICallback callback=this;
        Button loginButton = (Button) findViewById(R.id.goLogin);
        loginButton.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
            	EditText user = (EditText) findViewById(R.id.loginfield);
            	EditText pass = (EditText) findViewById(R.id.passfield);
            	String[] loginfo = {user.getText().toString(),pass.getText().toString()};
            	if (loginfo[0].equals("")){
            		Toast.makeText(getApplicationContext(), getString(R.string.empty_user), 5).show();
            		return;
            	}
            	if (loginfo[1].equals("")){
            		Toast.makeText(getApplicationContext(), getString(R.string.empty_pass), 5).show();
            		return;
            	}
            	new APICall(callback).execute("login?user="+loginfo[0]+"&pass="+loginfo[1]);
            }
        });
    }
    
	@Override
	public void APIResponse(JSONObject json) {
		// TODO Auto-generated method stub
		
	}
    
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();
        if (id == R.id.action_settings) {
            return true;
        }
        return super.onOptionsItemSelected(item);
    }
}
