package com.scopyk.fydeph.data;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Date;
import java.util.HashMap;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class Content {
	private static Content c=null;
	public static Content get(){
		if (c==null) c=new Content();
		return c;
	}
	
	private HashMap<String,Folder> folders;
	private HashMap<String,Feed> feeds;
	private HashMap<String,Tag> tags;
	private HashMap<String,Post> posts;
	private List<Post> postsOrdered;
	private String token,lock;
	private int totalPosts;
	
	private Filter filter;
	
	private Content(){
		this.folders = new HashMap<String,Folder>();
		this.feeds = new HashMap<String,Feed>();
		this.tags = new HashMap<String,Tag>();
		this.posts = new HashMap<String,Post>();
		this.token = "";
		this.totalPosts = 0;
		this.postsOrdered = new ArrayList<Post>();
	}
	
	public void setToken(String token){
		this.token = token;
		this.filter = new Filter(token);
	}
	public void setLock(String lock){
		this.lock = lock;
		this.filter.viewUnlocked(lock);
	}
	public String getToken(){
		return this.token;
	}
	public String getLock(){
		return this.lock;
	}
	
	public void reloadStructure(JSONObject json) throws JSONException{
		this.folders = new HashMap<String,Folder>();
		this.feeds = new HashMap<String,Feed>();
		this.tags = new HashMap<String,Tag>();
		this.posts = new HashMap<String,Post>();
		this.postsOrdered = new ArrayList<Post>();
		
		JSONArray folders = json.getJSONArray("folders");
		for (int i=0;i<folders.length();i++){
			JSONObject f = (JSONObject)folders.get(i);
			Folder folder = new Folder(f.getString("id"),f.getString("name"));
			folder.setHidden(f.getInt("hidden")==1);
			folder.setUnread(f.getInt("unread"));
			folder.setCount(f.getInt("count"));
			JSONArray feeds = f.getJSONArray("feeds");
			for (int j=0;j<feeds.length();j++){
				JSONObject ff = (JSONObject)feeds.get(j);
				
				Feed feed = new Feed(ff.getString("id"),ff.getString("name"));
				feed.setLink(ff.getString("link"));
				feed.setRssLink(ff.getString("rss_link"));
				feed.setCount(ff.getInt("count"));
				feed.setUnread(ff.getInt("unread"));
				//ff.getInt("last_date_post")
				//ff.getInt("upd_time")
				
				feed.setFolder(folder);
				folder.addFeeds(feed);
				this.feeds.put(ff.getString("id"),feed);
			}
			this.folders.put(f.getString("id"),folder);
		}
		
		JSONArray tags = json.getJSONArray("tags");
		for (int i=0;i<tags.length();i++){
			JSONObject t = (JSONObject)tags.get(i);
			Tag tag = new Tag(t.getString("id"),t.getString("name"));
			tag.setCount(t.getInt("count"));
			tag.setPublic(t.getInt("public")==1);
			tag.setHidden(t.getInt("hidden")==1);

			this.tags.put(t.getString("id"),tag);
		}
	}
	
	public void resetPosts(){
		this.posts = new HashMap<String,Post>();
		this.postsOrdered = new ArrayList<Post>();
		this.totalPosts = 0;
	}
	
	public void addPosts(JSONObject json) throws JSONException{
		if (json.has("total"))
			this.totalPosts = json.getInt("total");

		SimpleDateFormat sdf = new SimpleDateFormat("yyyy-M-dd hh:mm:ss");

		JSONArray posts = json.getJSONArray("posts");
		for (int i=0;i<posts.length();i++){
			JSONObject p = (JSONObject)posts.get(i);
			Post post = new Post(p.getString("id"),decodeHtml(p.getString("title"),true),p.getString("description"));
			post.setLink(p.getString("link"));
			post.setFavorite(p.getInt("favorite")==1);
			post.setUnread(p.getInt("unread")==1);
			try {
				post.setDate(sdf.parse(p.getString("date")));
			} catch (ParseException e) {
				post.setDate(null);
				e.printStackTrace();
			}
			String feedid = p.getString("feedId");
			if (this.feeds.containsKey(feedid)){
				post.setFeed(this.feeds.get(feedid));
			} else {
				post.setFeed(null);
			}
			
			JSONArray tags = p.getJSONArray("tags"); 
			for (int j=0;j<tags.length();j++){
				JSONObject t = (JSONObject)tags.get(j);
				Tag tag;
				String tagid = t.getString("id");
				if (this.tags.containsKey(tagid)){
					tag = this.tags.get(tagid);
				} else {
					tag = new Tag(tagid,t.getString("name"));
					this.tags.put(tagid, tag);
				}
				tag.addPost(post);
				post.addTag(tag);
			}
			this.posts.put(p.getString("id"),post);
			this.postsOrdered.add(post);
		}
	}

	public HashMap<String, Folder> getFolders() {
		return folders;
	}
	public Folder getFolder(String id) {
		return folders.get(id);
	}
	public HashMap<String, Tag> getTags() {
		return tags;
	}
	public Tag getTag(String id) {
		return tags.get(id);
	}
	public HashMap<String, Post> getPosts() {
		return posts;
	}
	public Post getPost(String id) {
		return posts.get(id);
	}
	public List<Post> getOrderedPosts() {
		return postsOrdered;
	}
	public Post getNextPost(Post p){
		int idx = postsOrdered.indexOf(p);
		idx++;
		if (idx==postsOrdered.size())
			return p;
		else
			return postsOrdered.get(idx);
	}
	public Post getPrevPost(Post p){
		int idx = postsOrdered.indexOf(p);
		idx--;
		if (idx<0)
			return p;
		else
			return postsOrdered.get(idx);
	}
	public String decodeHtml(String in){
		return decodeHtml(in,false);
	}
	public String decodeHtml(String in,boolean nobreak){
		String str = in;
		if (nobreak)
			str = in.replace("\n", " ");
		return android.text.Html.fromHtml(str).toString();
	}
	
	
	
	public void viewFolder(String id){
		this.filter.viewFolder(id);
	}
	public void viewFeed(String id){
		this.filter.viewFeed(id);
	}
	public void viewTag(String id){
		this.filter.viewTag(id);
	}
	public void viewAll(){
		this.filter.viewAll();
	}
	public void viewOldersFirst(){
		viewNewersFirst(false);
	}
	public void viewNewersFirst(){
		viewNewersFirst(true);
	}
	public void viewNewersFirst(boolean val){
		this.filter.viewNewersFirst(val);
	}
	public void viewUnlocked(String st){
		this.filter.viewUnlocked(st);
	}
	public void viewUnread(boolean u){
		this.filter.viewUnread(u);
	}
	public void viewFavorites(boolean f){
		this.filter.viewFavorites(f);
	}
	public boolean toggleFavorites(){
		boolean i = this.filter.isFavorite();
		this.filter.viewFavorites(!i);
		return !i;
	}
	public boolean toggleUnread(){
		boolean i = this.filter.isUnread();
		this.filter.viewUnread(!i);
		return !i;
	}
	public boolean toggleOrder(){
		boolean i = this.filter.isNewFirst();
		this.filter.viewNewersFirst(!i);
		return !i;
	}
	public String getQuery(String nextValue){
		return this.filter.getQuery(nextValue);
	}
	public String getQuery(){
		return this.filter.getQuery("");
	}
	
}
