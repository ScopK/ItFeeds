package com.scopyk.fydeph.view;

import org.json.JSONException;
import org.json.JSONObject;

import com.scopyk.fydeph.APICall;
import com.scopyk.fydeph.APICallback;
import com.scopyk.fydeph.R;
import com.scopyk.fydeph.R.id;
import com.scopyk.fydeph.R.layout;
import com.scopyk.fydeph.R.menu;
import com.scopyk.fydeph.R.string;
import com.scopyk.fydeph.data.Content;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.TextView.OnEditorActionListener;
import android.widget.Toast;


public class LoginActivity extends Activity implements APICallback {
	
	
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        
        EditText pass = (EditText) findViewById(R.id.passfield);
        pass.setOnEditorActionListener(new OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                boolean handled = false;
                if (actionId == EditorInfo.IME_ACTION_DONE) {
                	findViewById(R.id.goLogin).performClick();
                }
                return handled;
            }
        });
        loginSetUp();
        
        String tokenRead = loadToken();
        if (tokenRead != null){
            Content.get().setToken(tokenRead);
            Intent intentApp = new Intent(this, MainActivity.class);
            startActivity(intentApp);
        }
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
            	Button button = (Button) findViewById(R.id.goLogin);
            	button.setText(R.string.logingin);
            	button.setEnabled(false);
            	new APICall(callback).execute("login?user="+loginfo[0]+"&pass="+loginfo[1]);
            }
        });
    }
    
	@Override
	public void APIResponse(JSONObject json, int id) throws JSONException {
		Button button = (Button) findViewById(R.id.goLogin);
    	button.setText(R.string.login);
    	button.setEnabled(true);
    	
		if (json.has("error")){
			String errormsg = json.getString("error"); 
			if (errormsg.equals("No connection"))
				Toast.makeText(this.getApplicationContext(), R.string.no_connection, 5).show();
			else if (errormsg.equals("Incorrect login"))
				Toast.makeText(this.getApplicationContext(), R.string.unknown_user, 5).show(); //
			else
				Toast.makeText(this.getApplicationContext(), errormsg, 5).show();
			return;
		}
		String tokenObtained = json.getString("token");
		saveToken(tokenObtained);
		
        Content.get().setToken(tokenObtained);
        Intent intentApp = new Intent(this, MainActivity.class);
        startActivity(intentApp);
	}
	
	public void saveToken(String token){
	      SharedPreferences settings = getSharedPreferences("FydephPrefsFile", 0);
	      SharedPreferences.Editor editor = settings.edit();
	      editor.putString("tokensaved", token);
	      editor.commit();
	}
	
	public String loadToken(){
        SharedPreferences settings = getSharedPreferences("FydephPrefsFile", 0);
        return settings.getString("tokensaved", null);
	}
    
}
