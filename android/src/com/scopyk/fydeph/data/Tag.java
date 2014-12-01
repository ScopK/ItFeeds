package com.scopyk.fydeph.data;

import java.util.ArrayList;

public class Tag implements MenuLabel {
	private String id;
	private String name;
	private ArrayList<Post> posts;
	private boolean hidden;
	private boolean ispublic;
	private int count;
	
	public Tag(String id,String name,boolean hidden, boolean ispublic,int count){
		this(id,name);
		this.hidden = hidden;
		this.ispublic = ispublic;
		this.count = count;
	}
	
	public Tag(String id,String name){
		this.id = id;
		this.name = name;
		this.posts = new ArrayList<Post>();
	}
	
	public ArrayList<Post> getPosts() {
		return posts;
	}
	public void setPosts(ArrayList<Post> posts) {
		this.posts = posts;
	}
	public void addPost(Post post) {
		this.posts.add(post);
	}
	public boolean isHidden() {
		return hidden;
	}
	public void setHidden(boolean hidden) {
		this.hidden = hidden;
	}
	public boolean isPublic() {
		return ispublic;
	}
	public void setPublic(boolean ispublic) {
		this.ispublic = ispublic;
	}
	public int getCount() {
		return count;
	}
	public void setCount(int count) {
		this.count = count;
	}
	public String getId() {
		return id;
	}
	public String getName() {
		return name;
	}
	
	@Override
	public String getLabel() {
		return this.name+" ("+this.count+")";
	}
	
	@Override
	public String getTitle() {
		return this.name;
	}
}
